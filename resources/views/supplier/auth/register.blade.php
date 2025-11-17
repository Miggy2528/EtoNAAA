@extends('layouts.auth')

@push('page-styles')
<style>
    /* Make the auth card centered and nicely sized */
    .auth-card {
        max-width: 600px;
        margin: auto;
    }
    @media (max-width: 991.98px) {
        .auth-card {
            max-width: 100%;
            padding: 0 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center g-4">
    <div class="col-12 col-lg-8">
        <form class="card card-md auth-card" action="{{ route('supplier.register.store') }}" method="POST" autocomplete="off">
            @csrf

            <div class="card-body">
                <h2 class="card-title text-center mb-4">Supplier Registration</h2>

                <x-input name="name" :value="old('name')" placeholder="Your name" required="true"/>
                <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>
                <x-input name="username" :value="old('username')" placeholder="Your username" required="true"/>
                
                <x-input type="password" name="password" :value="old('password')" placeholder="Password" required="true"/>
                <x-input type="password" name="password_confirmation" :value="old('password_confirmation')" placeholder="Password confirmation" required="true" label="Password Confirmation"/>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="terms-of-service" id="terms-of-service"
                               class="form-check-input @error('terms-of-service') is-invalid @enderror"
                        >
                        <span class="form-check-label">
                            Agree to the <a href="#" tabindex="-1">
                                terms and policy</a>.
                        </span>
                    </label>
                </div>

                <div class="form-footer">
                    <x-button type="submit" class="w-100">
                        {{ __('Create Supplier Account') }}
                    </x-button>
                </div>
            </div>
        </form>

        <div class="text-center text-secondary mt-3">
            Already have a supplier account? <a href="{{ route('supplier.login') }}" tabindex="-1">
                Sign in
            </a>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="text-center">
            <hr class="divider-hr">
            <p class="text-muted small">
                <strong>Supplier Portal:</strong> For suppliers who provide products to Yannis Meatshop
            </p>
            <a href="{{ route('welcome') }}" class="text-primary hover:text-primary-dark">
                Back to main login
            </a>
        </div>
    </div>
</div>
@endsection
