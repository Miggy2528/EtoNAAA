<?php

namespace App\Http\Requests\Auth;

use App\Services\AdminAuthService;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    protected $adminAuthService;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->adminAuthService = app(AdminAuthService::class);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Check if account is locked BEFORE attempting authentication
        if ($this->adminAuthService->isAccountLocked($this->email)) {
            // Account is locked, show lockout message with minutes countdown
            $secondsRemaining = $this->adminAuthService->getLockoutSecondsRemaining($this->email);
            $minutesRemaining = ceil($secondsRemaining / 60);
            $message = 'Account temporarily locked due to multiple failed login attempts. Please try again in ' . $minutesRemaining . ' minutes.';
            
            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        // Only attempt authentication if account is not locked
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            
            // Get current failed attempts count BEFORE logging this attempt
            $currentFailedAttempts = $this->adminAuthService->getFailedAttemptsCount($this->email);
            
            // Log failed attempt
            $this->adminAuthService->logFailedAttempt($this->email);
            
            // Calculate total failed attempts including this one
            $totalFailedAttempts = $currentFailedAttempts + 1;
            
            // Check if this attempt caused a lockout (3rd failed attempt)
            if ($totalFailedAttempts >= 3) {
                // Account is now locked
                $message = 'Account temporarily locked due to multiple failed login attempts. Please try again in 5 minutes.';
            } else {
                // Get the appropriate warning message based on failed attempts
                $warning = $this->adminAuthService->getWarningMessage($this->email, $totalFailedAttempts);
                $message = trans('auth.failed');
                
                if (!empty($warning['message'])) {
                    $message .= ' ' . $warning['message'];
                }
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        // Check if the authenticated user is a supplier (suppliers should use supplier login)
        $user = Auth::user();
        if ($user->role === 'supplier') {
            Auth::logout();
            
            throw ValidationException::withMessages([
                'email' => 'This is the Admin/Staff login. Suppliers should use the Supplier Login portal.',
            ]);
        }

        // Check if the authenticated user is allowed (admin or staff only)
        if (!in_array($user->role, ['admin', 'staff'])) {
            Auth::logout();
            
            throw ValidationException::withMessages([
                'email' => 'You are not authorized to access the admin/staff area.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        // Log successful attempt
        $this->adminAuthService->logSuccessfulAttempt($this->email);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}