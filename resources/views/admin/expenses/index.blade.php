@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Expenses</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.report.index') }}"><div class="text-tiny">Report</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Expenses</div></li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">

            {{-- ── Add Expense Form ── --}}
            <div class="col-lg-4">
                <div class="wg-box">
                    <h5 class="mb-4">Record New Expense</h5>
                    <form action="{{ route('admin.expense.store') }}" method="POST">
                        @csrf
                        <fieldset class="name mb-20">
                            <div class="body-title mb-10">Description <span class="tf-color-1">*</span></div>
                            <input type="text" name="description" class="flex-grow @error('description') is-invalid @enderror"
                                   placeholder="e.g. Delivery charges" value="{{ old('description') }}" required>
                            @error('description')<div class="text-danger text-tiny mt-1">{{ $message }}</div>@enderror
                        </fieldset>

                        <fieldset class="name mb-20">
                            <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                            <select name="category" class="flex-grow @error('category') is-invalid @enderror" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')<div class="text-danger text-tiny mt-1">{{ $message }}</div>@enderror
                        </fieldset>

                        <fieldset class="name mb-20">
                            <div class="body-title mb-10">Amount (Rs) <span class="tf-color-1">*</span></div>
                            <input type="number" name="amount" class="flex-grow @error('amount') is-invalid @enderror"
                                   step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount') }}" required>
                            @error('amount')<div class="text-danger text-tiny mt-1">{{ $message }}</div>@enderror
                        </fieldset>

                        <fieldset class="name mb-20">
                            <div class="body-title mb-10">Date <span class="tf-color-1">*</span></div>
                            <input type="date" name="expense_date" class="flex-grow @error('expense_date') is-invalid @enderror"
                                   value="{{ old('expense_date', now()->toDateString()) }}" required>
                            @error('expense_date')<div class="text-danger text-tiny mt-1">{{ $message }}</div>@enderror
                        </fieldset>

                        @if($branches->count())
                        <fieldset class="name mb-20">
                            <div class="body-title mb-10">Branch</div>
                            <select name="branch_id" class="flex-grow">
                                <option value="">— All / General —</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                        @endif

                        <fieldset class="name mb-20">
                            <div class="body-title mb-10">Notes (optional)</div>
                            <textarea name="notes" class="flex-grow" rows="2" placeholder="Any additional detail…">{{ old('notes') }}</textarea>
                        </fieldset>

                        <button type="submit" class="tf-button style-1 w-full">Save Expense</button>
                    </form>
                </div>
            </div>

            {{-- ── Expenses List ── --}}
            <div class="col-lg-8">
                <div class="wg-box">
                    <div class="flex items-center justify-between gap20 mb-20">
                        <h5>Expense Records</h5>
                        <div style="font-size:13px;color:#888;">
                            Showing filtered total: <strong style="color:#be123c;">Rs {{ number_format($totalAmount, 2) }}</strong>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" class="d-flex gap-2 mb-20 flex-wrap">
                        <input type="date" name="date_from" class="flex-grow" style="max-width:160px;"
                               value="{{ request('date_from') }}" placeholder="From">
                        <input type="date" name="date_to" class="flex-grow" style="max-width:160px;"
                               value="{{ request('date_to') }}" placeholder="To">
                        @if($branches->count())
                        <select name="branch_id" class="flex-grow" style="max-width:160px;">
                            <option value="">All Branches</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @endif
                        <button type="submit" class="tf-button style-1">Filter</button>
                        <a href="{{ route('admin.expenses') }}" class="tf-button style-2">Clear</a>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="font-size:13px;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Branch</th>
                                    <th class="text-end">Amount (Rs)</th>
                                    <th class="text-center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $exp)
                                <tr>
                                    <td>{{ $exp->expense_date->format('d M Y') }}</td>
                                    <td>
                                        {{ $exp->description }}
                                        @if($exp->notes)
                                            <div style="font-size:11px;color:#888;">{{ $exp->notes }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $exp->category }}</span></td>
                                    <td>{{ $exp->branch?->name ?? '—' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($exp->amount, 2) }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.expense.delete', $exp->id) }}" method="POST"
                                              onsubmit="return confirm('Delete this expense?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" style="padding:3px 10px;font-size:11px;">
                                                <i class="icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No expenses found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $expenses->links() }}</div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
