@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Branch</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.branches') }}"><div class="text-tiny">Branches</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit</div></li>
            </ul>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-20">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif

        <div class="wg-box">
            <form action="{{ route('admin.branch.update', $branch->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row gap20">
                    <div class="col-md-6">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Branch Name <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="name" value="{{ old('name', $branch->name) }}" required>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Branch Code <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="code" value="{{ old('code', $branch->code) }}" required>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">City</div>
                            <input class="flex-grow" type="text" name="city" value="{{ old('city', $branch->city) }}">
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Phone</div>
                            <input class="flex-grow" type="text" name="phone" value="{{ old('phone', $branch->phone) }}">
                        </fieldset>
                    </div>
                    <div class="col-12">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Address</div>
                            <input class="flex-grow" type="text" name="address" value="{{ old('address', $branch->address) }}">
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Status</div>
                            <select name="is_active" class="flex-grow">
                                <option value="1" {{ old('is_active', $branch->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $branch->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </fieldset>
                    </div>
                </div>
                <div class="flex gap10 mt-10">
                    <button type="submit" class="tf-button w208">Update Branch</button>
                    <a href="{{ route('admin.branches') }}" class="tf-button style-2 w208">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
