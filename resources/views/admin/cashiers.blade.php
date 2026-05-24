@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Cashier Management</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Cashiers</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-20">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif

        <div class="row gap20">
            {{-- Add Cashier Form --}}
            <div class="col-lg-4">
                <div class="wg-box">
                    <h5 class="mb-20">Add POS User</h5>
                    <form action="{{ route('admin.cashier.store') }}" method="POST">
                        @csrf
                        <fieldset class="name mb-16">
                            <div class="body-title mb-8">Full Name <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="name" value="{{ old('name') }}" required>
                        </fieldset>
                        <fieldset class="name mb-16">
                            <div class="body-title mb-8">Email <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="email" name="email" value="{{ old('email') }}" required>
                        </fieldset>
                        <fieldset class="name mb-16">
                            <div class="body-title mb-8">Mobile <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="20">
                        </fieldset>
                        <fieldset class="name mb-16">
                            <div class="body-title mb-8">Password <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="password" name="password" required minlength="6">
                        </fieldset>
                        <fieldset class="name mb-16">
                            <div class="body-title mb-8">Role <span class="tf-color-1">*</span></div>
                            <select name="pos_role" class="flex-grow" required>
                                <option value="cashier" {{ old('pos_role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                                <option value="pos_supervisor" {{ old('pos_role') == 'pos_supervisor' ? 'selected' : '' }}>POS Supervisor</option>
                            </select>
                        </fieldset>
                        <fieldset class="name mb-16">
                            <div class="body-title mb-8">Branch</div>
                            <select name="branch_id" class="flex-grow">
                                <option value="">— No Branch —</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                        <button type="submit" class="tf-button w-full">Add User</button>
                    </form>
                </div>
            </div>

            {{-- Cashier List --}}
            <div class="col-lg-8">
                <div class="wg-box">
                    <h5 class="mb-20">POS Users</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Branch</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashiers as $cashier)
                                <tr>
                                    <td>{{ $cashier->name }}</td>
                                    <td><small>{{ $cashier->email }}</small></td>
                                    <td>
                                        @if($cashier->pos_role === 'pos_supervisor')
                                            <span class="badge bg-primary">Supervisor</span>
                                        @else
                                            <span class="badge bg-info">Cashier</span>
                                        @endif
                                    </td>
                                    <td>{{ $cashier->branch?->name ?? '—' }}</td>
                                    <td class="text-center">
                                        @if($cashier->pos_role)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Revoked</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="list-icon-function">
                                            <button type="button" class="item eye"
                                                onclick="openEditModal({{ $cashier->id }}, '{{ addslashes($cashier->name) }}', '{{ $cashier->pos_role }}', '{{ $cashier->branch_id }}')"
                                                title="Edit">
                                                <i class="icon-edit-3"></i>
                                            </button>
                                            <form action="{{ route('admin.cashier.revoke', $cashier->id) }}" method="POST"
                                                  onsubmit="return confirm('Revoke POS access for this user?')">
                                                @csrf @method('PUT')
                                                <button type="submit" style="background:none;border:none;padding:0;" title="Revoke">
                                                    <div class="item trash"><i class="icon-x-circle"></i></div>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-20">No POS users yet.</td></tr>
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
<div class="modal fade" id="editCashierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit POS User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCashierForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-16">
                        <div class="body-title mb-8">Role</div>
                        <select name="pos_role" id="edit_pos_role" class="flex-grow form-control">
                            <option value="cashier">Cashier</option>
                            <option value="pos_supervisor">POS Supervisor</option>
                        </select>
                    </div>
                    <div class="mb-16">
                        <div class="body-title mb-8">Branch</div>
                        <select name="branch_id" id="edit_branch_id" class="flex-grow form-control">
                            <option value="">— No Branch —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <div class="body-title mb-8">New Password (leave blank to keep)</div>
                        <input type="password" name="password" class="flex-grow form-control" minlength="6" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="tf-button">Update</button>
                    <button type="button" class="tf-button style-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, name, role, branchId) {
    $('#editCashierForm').attr('action', '/admin/cashier/update/' + id);
    $('#edit_pos_role').val(role);
    $('#edit_branch_id').val(branchId || '');
    $('#editCashierModal').modal('show');
}
</script>
@endpush
