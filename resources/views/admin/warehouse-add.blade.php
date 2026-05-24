@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>New Warehouse</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.warehouses') }}"><div class="text-tiny">Warehouses</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">New</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <form action="{{ route('admin.warehouse.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-500">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required maxlength="100" placeholder="e.g. Main Warehouse">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code') }}" required maxlength="20" placeholder="e.g. WH-KHI-01" style="text-transform:uppercase;">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Unique identifier (e.g. WH-KHI-01)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" maxlength="255" placeholder="Street address">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}" maxlength="100" placeholder="e.g. Karachi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Manager Name</label>
                        <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name') }}" maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Manager Phone</label>
                        <input type="text" name="manager_phone" class="form-control" value="{{ old('manager_phone') }}" maxlength="20">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="tf-button style-1 me-3">Create Warehouse</button>
                        <a href="{{ route('admin.warehouses') }}" class="tf-button" style="background:#6c757d;">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
