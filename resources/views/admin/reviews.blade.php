@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Product Reviews</h3>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="wg-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>
                                <a href="{{ route('shop.product.details', $review->product->slug) }}" target="_blank">
                                    {{ $review->product->name }}
                                </a>
                            </td>
                            <td>{{ $review->user->name }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </td>
                            <td>
                                @if($review->title)
                                    <strong>{{ $review->title }}</strong><br>
                                @endif
                                <small>{{ Str::limit($review->comment, 80) }}</small>
                            </td>
                            <td>
                                @if($review->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($review->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>{{ $review->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($review->status !== 'approved')
                                    <form action="{{ route('admin.review.approve', $review->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    @endif
                                    @if($review->status !== 'rejected')
                                    <form action="{{ route('admin.review.reject', $review->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-warning">Reject</button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.review.delete', $review->id) }}" method="POST"
                                          onsubmit="return confirm('Delete this review?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No reviews yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
