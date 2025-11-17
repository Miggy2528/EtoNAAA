@extends('layouts.auth')

@push('page-styles')
<style>
    /* Make the auth card centered and nicely sized */
    .auth-card {
        max-width: 800px; /* Adjust as needed */
        margin: auto;
    }
    @media (max-width: 991.98px) { /* Bootstrap lg breakpoint */
        .auth-card {
            max-width: 100%;
            padding: 0 15px;
        }
    }

    /* Center the divider line */
    .divider-hr {
        max-width: 400px;
        margin: 2rem auto;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center g-4">
    <!-- Admin/Staff Registration -->
    <div class="col-12 col-lg-6">
        <form class="card card-md auth-card" action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf

            <div class="card-body">
                <h2 class="card-title text-center mb-4">Staff/Admin Registration</h2>

                <x-input name="name" :value="old('name')" placeholder="Your name" required="true"/>
                <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>
                <x-input name="username" :value="old('username')" placeholder="Your username" required="true"/>
                
                <!-- Role Selection -->
                <div class="mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                        <label class="form-selectgroup-item flex-fill">
                            <input type="radio" name="role" value="admin" class="form-selectgroup-input" {{ old('role') == 'admin' ? 'checked' : '' }}>
                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                <div class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </div>
                                <div>
                                    <strong>Administrator</strong>
                                    <div class="text-muted">Full system access, user management, and all administrative functions</div>
                                </div>
                            </div>
                        </label>
                        <label class="form-selectgroup-item flex-fill">
                            <input type="radio" name="role" value="staff" class="form-selectgroup-input" {{ old('role') == 'staff' ? 'checked' : '' }}>
                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                <div class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </div>
                                <div>
                                    <strong>Staff</strong>
                                    <div class="text-muted">Inventory management, order processing, and basic system functions</div>
                                </div>
                            </div>
                        </label>
                        <label class="form-selectgroup-item flex-fill">
                            <input type="radio" name="role" value="supplier" class="form-selectgroup-input" {{ old('role') == 'supplier' ? 'checked' : '' }}>
                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                <div class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </div>
                                <div>
                                    <strong>Supplier</strong>
                                    <div class="text-muted">Product supply management and order fulfillment</div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <x-input name="password" :value="old('password')" placeholder="Password" required="true"/>
                <x-input name="password_confirmation" :value="old('password_confirmation')" placeholder="Password confirmation" required="true" label="Password Confirmation"/>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="terms-of-service" id="terms-of-service"
                               class="form-check-input @error('terms-of-service') is-invalid @enderror"
                        >
                        <span class="form-check-label">
                            Agree to the <a href="./terms-of-service.html" tabindex="-1">
                                terms and policy</a>.
                        </span>
                    </label>
                </div>

                <div class="form-footer">
                    <x-button type="submit" class="w-100">
                        {{ __('Create Staff/Admin Account') }}
                    </x-button>
                </div>
            </div>
        </form>

        <div class="text-center text-secondary mt-3">
            Staff/Admin? Already have account? <a href="{{ route('login') }}" tabindex="-1">
                Sign in
            </a>
        </div>
    </div>
</div>

<!-- Divider -->
<div class="row mt-4">
    <div class="col-12">
        <div class="text-center">
            <hr class="divider-hr">
            <p class="text-muted small">
                <strong>Staff/Admin:</strong> For employees who manage inventory, orders, and system settings
            </p>
        </div>
    </div>
</div>
@endsection
