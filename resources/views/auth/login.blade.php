@extends('layouts.app')

@section('content')
    <section class="oops-section section-padding white-bg fix">
        <div class="container">
            <div class="row g-0 justify-content-center">
                <div class="col-lg-6 d-center">
                    <div class="error-content text-center">
                        <div class="section-title text-center mb-3 text-lg-center">

                            <h3 class="fw-semibold black-clr fs-32 mb-lg-3 mb-2 wow fadeInUp" data-wow-delay=".3s">
                                Login To Your Account
                            </h3>

                            <form action="{{ route('login') }}" method="POST"
                                class="billing-form reservation-form p-0 needs-validation" novalidate="" id="login-form">
                                   @csrf
                                <div class="row g-lg-4 g-3">

                                    <div class="col-sm-12">
                                        <div class="form-group m-0">
                                            <input type="text" id="email" placeholder="Email *" name="email"
                                                value="{{ old('email') }}" required="" autocomplete="email"
                                                autofocus="">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group m-0">
                                            <input type="password" id="password" placeholder="Password *" name="password"
                                                required="" autocomplete="current-password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mt-4">
                                        <button type="submit" class="theme-btn">
                                            LOG IN
                                        </button>
                                    </div>
                                    {{-- <div class="mt-4 text-center">
                                        <span class="text-secondary">No account yet?</span>
                                        <a href="{{ route('register') }}" class="">Create Account</a> | <a
                                            href="{{ route('password.request') }}" class="">Forget Password?</a>
                                    </div> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
