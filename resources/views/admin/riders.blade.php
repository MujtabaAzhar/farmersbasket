@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Delivery Riders</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Riders</div></li>
            </ul>
        </div>

        @foreach(['success','error','warning'] as $t)
            @if(session($t))<div class="alert alert-{{ $t === 'error' ? 'danger' : $t }} mb-4">{{ session($t) }}</div>@endif
        @endforeach

        <div class="row g-4">

            {{-- Add Rider Form --}}
            <div class="col-md-4">
                <div class="wg-box">
                    <h5 class="mb-3">Add New Rider</h5>
                    <form action="{{ route('admin.riders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required maxlength="20">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vehicle *</label>
                            <div class="select">
                                <select name="vehicle_type" required>
                                    @foreach(App\Models\Rider::VEHICLES as $key => $label)
                                    <option value="{{ $key }}" {{ old('vehicle_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <div class="select">
                                <select name="branch_id">
                                    <option value="">— No specific branch —</option>
                                    @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="tf-button style-1 w-100">Add Rider</button>
                    </form>
                </div>
            </div>

            {{-- Riders Table --}}
            <div class="col-md-8">
                <div class="wg-box">
                    <h5 class="mb-3">All Riders <span class="text-muted fs-6">({{ $riders->count() }})</span></h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Vehicle</th>
                                    <th>Branch</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riders as $rider)
                                <tr>
                                    <td class="fw-600">{{ $rider->name }}</td>
                                    <td>{{ $rider->phone }}</td>
                                    <td>{{ $rider->vehicle_label }}</td>
                                    <td>{{ $rider->branch?->name ?? '—' }}</td>
                                    <td class="text-center">
                                        @if($rider->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-outline-secondary"
                                                    onclick="openEditModal({{ $rider->id }}, '{{ addslashes($rider->name) }}', '{{ $rider->phone }}', '{{ $rider->vehicle_type }}', {{ $rider->branch_id ?? 'null' }}, {{ $rider->is_active ? 1 : 0 }})">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.riders.destroy', $rider) }}" method="POST"
                                                  onsubmit="return confirm('Remove this rider?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">✕</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">No riders yet. Add one on the left.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editRiderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-rider-form" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" id="edit-phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vehicle *</label>
                        <select name="vehicle_type" id="edit-vehicle" class="form-select">
                            @foreach(App\Models\Rider::VEHICLES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" id="edit-branch" class="form-select">
                            <option value="">— No specific branch —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit-active" value="1">
                        <label class="form-check-label" for="edit-active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="tf-button style-1">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, phone, vehicle, branchId, isActive) {
    var base = '{{ url("admin/riders") }}';
    document.getElementById('edit-rider-form').action = base + '/' + id;
    document.getElementById('edit-name').value    = name;
    document.getElementById('edit-phone').value   = phone;
    document.getElementById('edit-vehicle').value = vehicle;
    document.getElementById('edit-branch').value  = branchId || '';
    document.getElementById('edit-active').checked = isActive == 1;
    new bootstrap.Modal(document.getElementById('editRiderModal')).show();
}
</script>
@endsection
