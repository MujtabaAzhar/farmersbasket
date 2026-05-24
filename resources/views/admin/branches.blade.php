@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Branches</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Branches</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-20">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                <h5>All Branches</h5>
                <a href="{{ route('admin.branch.add') }}" class="tf-button style-1">+ New Branch</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>City</th>
                            <th>Phone</th>
                            <th>Manager</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $branch->name }}</td>
                            <td><code>{{ $branch->code }}</code></td>
                            <td>{{ $branch->city }}</td>
                            <td>{{ $branch->phone ?? '—' }}</td>
                            <td>{{ $branch->manager?->name ?? '—' }}</td>
                            <td class="text-center">
                                @if($branch->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="list-icon-function">
                                    <a href="{{ route('admin.branch.edit', $branch->id) }}" title="Edit">
                                        <div class="item eye"><i class="icon-edit-3"></i></div>
                                    </a>
                                    <form action="{{ route('admin.branch.delete', $branch->id) }}" method="POST" onsubmit="return confirm('Delete this branch?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:none;border:none;padding:0;" title="Delete">
                                            <div class="item trash"><i class="icon-trash-2"></i></div>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-20">No branches found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
