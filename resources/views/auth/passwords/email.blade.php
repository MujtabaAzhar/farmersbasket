@extends('layouts.app')

@section('content')
<style>
    .auth-wrap {
        min-height: calc(100vh - 80px);
        display: flex;
    }
    .auth-left {
        flex: 0 0 42%;
        background: linear-gradient(150deg, #1a3a1a 0%, #2d6a2d 50%, #3d8b3d 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 48px;
        position: relative;
        overflow: hidden;
    }
    .auth-left::before {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -80px; right: -80px;
    }
    .auth-left::after {
        content: '';
        position: absolute;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        bottom: -60px; left: -60px;
    }
    .auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 32px;
        background: #f5f6f8;
    }
    .auth-card {
        background: #fff;
        border-radius: 16px;
        padding: 44px 40px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
    }
    .auth-input-wrap {
        position: relative;
        margin-bottom: 16px;
    }
    .auth-input-wrap .auth-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 15px;
        pointer-events: none;
    }
    .auth-input-wrap input {
        width: 100%;
        padding: 12px 42px 12px 42px;
        border: 1.5px solid #e0e0e0;
        border-radius: 10px;
        font-size: 14px;
        color: #333;
        background: #fafafa;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }
    .auth-input-wrap input:focus {
        border-color: #2d6a2d;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(45,106,45,0.1);
    }
    .auth-input-wrap input.is-invalid { border-color: #c62828; }
    .auth-error {
        color: #c62828;
        font-size: 12px;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .auth-btn {
        width: 100%;
        background: linear-gradient(135deg, #1a3a1a, #2d6a2d);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 13px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: opacity 0.2s;
        font-family: inherit;
    }
    .auth-btn:hover { opacity: 0.9; }
    .auth-divider {
        text-align: center;
        position: relative;
        margin: 20px 0;
        color: #bbb;
        font-size: 12px;
    }
    .auth-divider::before, .auth-divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 42%;
        height: 1px;
        background: #e8e8e8;
    }
    .auth-divider::before { left: 0; }
    .auth-divider::after  { right: 0; }
    .feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    .feature-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: rgba(255,255,255,0.12);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    @media (max-width: 991px) {
        .auth-left { display: none; }
        .auth-right { padding: 32px 16px; }
    }
</style>

<div class="auth-wrap">

    {{-- Left Brand Panel --}}
    <div class="auth-left">
        <div class="text-center mb-5 position-relative z-1">
            <h2 class="text-white fw-bold mt-3 mb-1" style="font-size:26px;">Farmer's Basket</h2>
            <p style="color:rgba(255,255,255,0.65);font-size:14px;">Fresh from the Farm to Your Door 🥭</p>
        </div>

        <div class="position-relative z-1 w-100" style="max-width:320px;">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-lock" style="color:#a5d6a7;font-size:16px;"></i>
                </div>
                <div>
                    <div class="text-white fw-semibold fs-14">Secure Reset</div>
                    <div style="color:rgba(255,255,255,0.6);font-size:12px;">Password reset link sent to your email</div>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-clock" style="color:#a5d6a7;font-size:16px;"></i>
                </div>
                <div>
                    <div class="text-white fw-semibold fs-14">Link Valid for 60 Min</div>
                    <div style="color:rgba(255,255,255,0.6);font-size:12px;">Use the link before it expires</div>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-lock" style="color:#a5d6a7;font-size:16px;"></i>
                </div>
                <div>
                    <div class="text-white fw-semibold fs-14">Your Data is Safe</div>
                    <div style="color:rgba(255,255,255,0.6);font-size:12px;">We never share your personal information</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Form Panel --}}
    <div class="auth-right">
        <div class="auth-card">

            {{-- Header --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:52px;height:52px;background:#e8f5e9;">
                    <i class="fa-solid fa-key" style="color:#2d6a2d;font-size:20px;"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1" style="font-size:22px;">Forgot Password?</h4>
                <p class="text-muted fs-14 mb-0">Enter your email and we'll send you a reset link</p>
            </div>

            {{-- Success Status --}}
            @if(session('status'))
            <div class="d-flex align-items-center gap-2 rounded-3 p-3 mb-4"
                 style="background:#e8f5e9;color:#2e7d32;font-size:13px;">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="auth-input-wrap">
                    <i class="fa-solid fa-envelope auth-icon"></i>
                    <input type="email" name="email" id="email" placeholder="Email address"
                           value="{{ old('email') }}" required autocomplete="email" autofocus
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                </div>
                @error('email')
                <div class="auth-error mb-2">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </div>
                @enderror

                <button type="submit" class="auth-btn mt-2">
                    <i class="fa-solid fa-paper-plane me-2"></i> Send Reset Link
                </button>
            </form>

            <div class="auth-divider">or</div>

            <div class="text-center">
                <span class="text-muted fs-14">Remember your password? </span>
                <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color:#2d6a2d;">
                    Sign In
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
