@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Warehouses</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Warehouses</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                <h5>All Warehouses</h5>
                <a href="{{ route('admin.warehouse.add') }}" class="tf-button style-1">+ New Warehouse</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>City</th>
                            <th>Manager</th>
                            <th class="text-center">Products</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $warehouse)
                        <tr>
                            <td>{{ $warehouse->id }}</td>
                            <td class="fw-500">{{ $warehouse->name }}</td>
                            <td><span class="badge bg-secondary">{{ $warehouse->code }}</span></td>
                            <td>{{ $warehouse->city ?: '—' }}</td>
                            <td>
                                {{ $warehouse->manager_name ?: '—' }}
                                @if($warehouse->manager_phone)
                                    <br><small class="text-muted">{{ $warehouse->manager_phone }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $warehouse->inventories_count }}</td>
                            <td class="text-center">
                                @if($warehouse->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="{{ route('admin.warehouse.inventory', $warehouse->id) }}" class="btn btn-sm btn-primary">Stock</a>
                                    <a href="{{ route('admin.warehouse.edit', $warehouse->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.warehouse.delete', $warehouse->id) }}" method="POST"
                                          onsubmit="return confirm('Delete warehouse {{ $warehouse->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No warehouses yet. <a href="{{ route('admin.warehouse.add') }}">Add the first one →</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $warehouses->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
