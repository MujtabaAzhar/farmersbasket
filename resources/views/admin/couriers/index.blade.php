@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Courier Services</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Courier Services</div></li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @foreach($couriers as $courier)
        <div class="wg-box mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="mb-0">{{ $courier->name }}</h5>
                    <small class="text-muted">Code: <code>{{ $courier->code }}</code> &nbsp;·&nbsp; {{ $courier->shipments_count }} shipments</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if($courier->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                    @if($courier->isConfigured())
                        <span class="badge bg-info">✅ API Configured</span>
                    @else
                        <span class="badge bg-warning text-dark">⚠ Manual Mode</span>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.couriers.update', $courier) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">API Key</label>
                        <input type="text" name="api_key" class="form-control"
                               placeholder="{{ $courier->isConfigured() ? '••••••••••••••••' : 'Enter API key' }}"
                               autocomplete="off">
                        <div class="form-text">Leave blank to keep existing.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">API Password / Secret</label>
                        <input type="password" name="api_password" class="form-control"
                               placeholder="{{ $courier->isConfigured() ? '••••••••••••••••' : 'Enter password/secret' }}"
                               autocomplete="new-password">
                        <div class="form-text">Leave blank to keep existing.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">API Base URL</label>
                        <input type="url" name="api_base_url" class="form-control"
                               value="{{ $courier->api_base_url }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Tracking URL Template</label>
                        <input type="text" name="tracking_url_template" class="form-control"
                               value="{{ $courier->tracking_url_template }}"
                               placeholder="https://example.com/track?cn={tracking_number}">
                        <div class="form-text">Use <code>{tracking_number}</code> as the placeholder.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="active_{{ $courier->id }}"
                                   value="1" {{ $courier->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="active_{{ $courier->id }}">Active</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="tf-button style-1">Save {{ $courier->name }}</button>
                    </div>
                </div>
            </form>
        </div>
        @endforeach

    </div>
</div>
@endsection
