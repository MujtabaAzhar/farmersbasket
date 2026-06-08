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
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif

        <div class="row gap20">

            {{-- ── Add Courier Form ──────────────────────────────────────── --}}
            <div class="col-lg-4">
                <div class="wg-box">
                    <h5 class="mb-20">Add Courier</h5>
                    <form action="{{ route('admin.couriers.store') }}" method="POST">
                        @csrf
                        <fieldset class="mb-16">
                            <div class="body-title mb-8">Company Name <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="name"
                                   value="{{ old('name') }}" placeholder="e.g. DHL, Leopards, TCS" required maxlength="100">
                        </fieldset>
                        <fieldset class="mb-16">
                            <div class="body-title mb-8">Tracking URL <span class="text-muted" style="font-weight:400;font-size:11px;">(optional)</span></div>
                            <input class="flex-grow" type="text" name="tracking_url_template"
                                   value="{{ old('tracking_url_template') }}"
                                   placeholder="https://track.example.com?cn={tracking_number}">
                            <div class="text-muted mt-1" style="font-size:11px;">Use <code>{tracking_number}</code> as placeholder.</div>
                        </fieldset>
                        <fieldset class="mb-20 d-flex align-items-center gap-2">
                            <input type="checkbox" name="is_active" id="add_is_active" value="1" checked style="width:16px;height:16px;">
                            <label for="add_is_active" class="body-title mb-0" style="cursor:pointer;">Active</label>
                        </fieldset>
                        <button type="submit" class="tf-button w-full">Add Courier</button>
                    </form>
                </div>
            </div>

            {{-- ── Courier List ──────────────────────────────────────────── --}}
            <div class="col-lg-8">
                <div class="wg-box">
                    <h5 class="mb-20">All Couriers
                        <span class="text-muted fw-normal fs-6 ms-2">({{ $couriers->count() }})</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Shipments</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($couriers as $courier)
                                <tr>
                                    <td>
                                        <div class="fw-600">{{ $courier->name }}</div>
                                        @if($courier->tracking_url_template)
                                            <div class="text-muted" style="font-size:11px;word-break:break-all;">
                                                🔗 {{ Str::limit($courier->tracking_url_template, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center"><code>{{ $courier->code }}</code></td>
                                    <td class="text-center">{{ $courier->shipments_count }}</td>
                                    <td class="text-center">
                                        @if($courier->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                        @if($courier->isConfigured())
                                            <span class="badge bg-info ms-1" style="font-size:10px;">API</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <div class="list-icon-function">
                                            <button type="button" title="Edit"
                                                onclick="openEdit({{ $courier->id }}, '{{ addslashes($courier->name) }}', '{{ addslashes($courier->tracking_url_template ?? '') }}', {{ $courier->is_active ? 'true' : 'false' }}, '{{ addslashes($courier->api_base_url ?? '') }}')"
                                                style="background:none;border:none;padding:0;cursor:pointer;">
                                                <div class="item eye"><i class="icon-edit-3"></i></div>
                                            </button>
                                            <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST"
                                                  onsubmit="return confirm('Delete {{ addslashes($courier->name) }}? This cannot be undone.')"
                                                  style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Delete" style="background:none;border:none;padding:0;cursor:pointer;">
                                                    <div class="item trash"><i class="icon-trash-2"></i></div>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No couriers added yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Edit Modal ──────────────────────────────────────────────────────── --}}
<div class="modal fade" id="editCourierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Courier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourierForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-600">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required maxlength="100">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Tracking URL Template</label>
                            <input type="text" name="tracking_url_template" id="edit_tracking_url" class="form-control"
                                   placeholder="https://track.example.com?cn={tracking_number}">
                            <div class="form-text">Use <code>{tracking_number}</code> as placeholder.</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>

                        {{-- API Credentials (collapsible) --}}
                        <div class="col-12">
                            <hr>
                            <div class="d-flex align-items-center justify-content-between" style="cursor:pointer;"
                                 onclick="toggleApiSection()">
                                <span class="fw-600 text-muted small text-uppercase" style="letter-spacing:.5px;">
                                    API Credentials (optional)
                                </span>
                                <span id="api-toggle-icon" class="small text-primary">▼ Show</span>
                            </div>
                            <div id="api-section" style="display:none;" class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">API Key</label>
                                    <input type="text" name="api_key" class="form-control"
                                           placeholder="Leave blank to keep existing" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">API Password / Secret</label>
                                    <input type="password" name="api_password" class="form-control"
                                           placeholder="Leave blank to keep existing" autocomplete="new-password">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">API Base URL</label>
                                    <input type="url" name="api_base_url" id="edit_api_base_url" class="form-control"
                                           placeholder="https://api.example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="tf-button">Save Changes</button>
                    <button type="button" class="tf-button style-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(id, name, trackingUrl, isActive, apiBaseUrl) {
    document.getElementById('editCourierForm').action = '/admin/couriers/' + id;
    document.getElementById('edit_name').value         = name;
    document.getElementById('edit_tracking_url').value = trackingUrl;
    document.getElementById('edit_is_active').checked  = isActive;
    document.getElementById('edit_api_base_url').value = apiBaseUrl;

    // Reset API section
    document.getElementById('api-section').style.display = 'none';
    document.getElementById('api-toggle-icon').textContent = '▼ Show';

    $('#editCourierModal').modal('show');
}

function toggleApiSection() {
    var sec  = document.getElementById('api-section');
    var icon = document.getElementById('api-toggle-icon');
    var open = sec.style.display !== 'none';
    sec.style.display  = open ? 'none' : 'block';
    icon.textContent   = open ? '▼ Show' : '▲ Hide';
}
</script>
@endpush
