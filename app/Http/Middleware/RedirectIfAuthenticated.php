<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // If it's a customer, redirect to customer dashboard
                if ($user instanceof \App\Models\Customer) {
                    return redirect()->route('customer.dashboard');
                }
                
                // If it's a supplier (User model with supplier role), redirect to supplier dashboard
                if ($user instanceof \App\Models\User && $user->role === 'supplier') {
                    return redirect()->route('supplier.dashboard');
                }
                
                // For regular users (admin/staff), redirect to main dashboard
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
