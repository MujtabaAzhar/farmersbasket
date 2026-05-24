@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Account Settings</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Settings</div></li>
            </ul>
        </div>

        <div class="row g-4">

            {{-- ── Profile ────────────────────────────────────────── --}}
            <div class="col-lg-6">
                <div class="wg-box">
                    <h5 class="mb-1" style="font-size:15px;font-weight:700;">Profile Information</h5>
                    <p class="text-muted fs-13 mb-4">Update your name, email address and phone number.</p>

                    @if(session('profile_success'))
                        <div class="alert alert-success py-2 fs-13">{{ session('profile_success') }}</div>
                    @endif

                    <form action="{{ route('admin.settings.profile') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="body-title mb-1">Full Name <span class="tf-color-1">*</span></label>
                            <input type="text" name="name" class="flex-grow @error('name') is-invalid @enderror"
                                   value="{{ old('name', $admin->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="body-title mb-1">Email Address <span class="tf-color-1">*</span></label>
                            <input type="email" name="email" class="flex-grow @error('email') is-invalid @enderror"
                                   value="{{ old('email', $admin->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="body-title mb-1">Mobile / Phone</label>
                            <input type="text" name="mobile" class="flex-grow @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', $admin->mobile) }}" placeholder="e.g. 03001234567">
                            @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="tf-button style-1">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Password ───────────────────────────────────────── --}}
            <div class="col-lg-6">
                <div class="wg-box">
                    <h5 class="mb-1" style="font-size:15px;font-weight:700;">Change Password</h5>
                    <p class="text-muted fs-13 mb-4">Use a strong password with at least 8 characters.</p>

                    @if(session('password_success'))
                        <div class="alert alert-success py-2 fs-13">{{ session('password_success') }}</div>
                    @endif

                    <form action="{{ route('admin.settings.password') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="body-title mb-1">Current Password <span class="tf-color-1">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="current_password" id="pwd-current"
                                       class="flex-grow @error('current_password') is-invalid @enderror"
                                       autocomplete="current-password" required>
                                <button type="button" class="pwd-toggle" data-target="pwd-current"
                                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#888;font-size:16px;">
                                    <i class="icon-eye"></i>
                                </button>
                            </div>
                            @error('current_password')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="body-title mb-1">New Password <span class="tf-color-1">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="new_password" id="pwd-new"
                                       class="flex-grow @error('new_password') is-invalid @enderror"
                                       autocomplete="new-password" required minlength="8">
                                <button type="button" class="pwd-toggle" data-target="pwd-new"
                                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#888;font-size:16px;">
                                    <i class="icon-eye"></i>
                                </button>
                            </div>
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="body-title mb-1">Confirm New Password <span class="tf-color-1">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="new_password_confirmation" id="pwd-confirm"
                                       class="flex-grow" autocomplete="new-password" required minlength="8">
                                <button type="button" class="pwd-toggle" data-target="pwd-confirm"
                                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#888;font-size:16px;">
                                    <i class="icon-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div id="pwd-strength" class="mb-3" style="display:none;">
                            <div style="height:4px;border-radius:2px;background:#eee;overflow:hidden;">
                                <div id="pwd-strength-bar" style="height:100%;width:0;transition:width .3s,background .3s;"></div>
                            </div>
                            <div id="pwd-strength-label" class="fs-12 mt-1 text-muted"></div>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="tf-button style-1">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Account Info (read-only) ───────────────────────── --}}
            <div class="col-lg-6">
                <div class="wg-box">
                    <h5 class="mb-1" style="font-size:15px;font-weight:700;">Account Details</h5>
                    <p class="text-muted fs-13 mb-4">Read-only information about your admin account.</p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted fs-12 mb-1">Account ID</div>
                            <div class="fw-600">#{{ $admin->id }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-12 mb-1">Role</div>
                            <div>
                                <span class="badge" style="background:#2ecc71;">{{ $admin->roleBadge() }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-12 mb-1">Account Created</div>
                            <div class="fw-600">{{ $admin->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-12 mb-1">Last Updated</div>
                            <div class="fw-600">{{ $admin->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-12 mb-1">Email Verified</div>
                            <div>
                                @if($admin->email_verified_at)
                                    <span class="badge bg-success">Verified</span>
                                    <span class="fs-12 text-muted ms-1">{{ \Carbon\Carbon::parse($admin->email_verified_at)->format('d M Y') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unverified</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted fs-12 mb-1">Account Type</div>
                            <div class="fw-600">{{ $admin->utype }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
// Password visibility toggles
document.querySelectorAll('.pwd-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = document.getElementById(this.dataset.target);
        var icon  = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('icon-eye', 'icon-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('icon-eye-off', 'icon-eye');
        }
    });
});

// Password strength indicator
document.getElementById('pwd-new').addEventListener('input', function() {
    var val   = this.value;
    var wrap  = document.getElementById('pwd-strength');
    var bar   = document.getElementById('pwd-strength-bar');
    var label = document.getElementById('pwd-strength-label');

    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';

    var score = 0;
    if (val.length >= 8)               score++;
    if (val.length >= 12)              score++;
    if (/[A-Z]/.test(val))             score++;
    if (/[0-9]/.test(val))             score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    var levels = [
        { pct: '20%', color: '#e74c3c', text: 'Very Weak' },
        { pct: '40%', color: '#e67e22', text: 'Weak' },
        { pct: '60%', color: '#f1c40f', text: 'Fair' },
        { pct: '80%', color: '#2ecc71', text: 'Strong' },
        { pct: '100%', color: '#27ae60', text: 'Very Strong' },
    ];
    var lvl = levels[Math.min(score, 4)];
    bar.style.width      = lvl.pct;
    bar.style.background = lvl.color;
    label.textContent    = lvl.text;
    label.style.color    = lvl.color;
});
</script>
@endpush
@endsection
