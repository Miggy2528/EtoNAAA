<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Rules\UniqueEmailAcrossTables;

class SupplierAuthController extends Controller
{
    /**
     * Display the supplier login view.
     */
    public function showLoginForm(): View
    {
        return view('supplier.auth.login');
    }

    /**
     * Handle supplier login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // First, find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists, has supplier role, and is active
        if (!$user || $user->role !== 'supplier' || $user->status !== 'active') {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records or your account is not active.',
            ])->onlyInput('email');
        }

        // Attempt login with just email and password
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('supplier.dashboard'))
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is not active.',
        ])->onlyInput('email');
    }

    /**
     * Display the supplier registration view.
     */
    public function showRegistrationForm(): View
    {
        return view('supplier.auth.register');
    }

    /**
     * Handle supplier registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class, 'alpha_dash:ascii'],
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossTables],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms-of-service' => ['required']
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'supplier',
            'status' => 'active',
        ]);

        // Create supplier record linked to user
        \App\Models\Supplier::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'contact_person' => $request->name,
            'phone' => null, // Can be updated later in profile
            'address' => null, // Can be updated later in profile
            'shopname' => $request->name . '\'s Shop',
            'type' => 'wholesaler', // Default type
            'status' => 'active',
            'account_holder' => $request->name,
            'account_number' => null,
            'bank_name' => null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('supplier.dashboard')
            ->with('success', 'Registration successful! Welcome to your supplier dashboard.');
    }

    /**
     * Handle supplier logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('welcome')
            ->with('success', 'You have been logged out successfully.');
    }
}
