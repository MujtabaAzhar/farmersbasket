@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Login Activity</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Login Activity</div></li>
            </ul>
        </div>

        {{-- Filter form --}}
        <div class="wg-box mb-20">
            <form method="GET" action="{{ route('admin.login.activity') }}" class="flex items-center gap10 flex-wrap">
                <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter by email..." style="border:1px solid #ddd; border-radius:6px; padding:6px 12px; font-size:13px;">
                <select name="action" style="border:1px solid #ddd; border-radius:6px; padding:6px 10px; font-size:13px;">
                    <option value="">All Actions</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="failed" {{ request('action') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button type="submit" class="tf-button style-1">Filter</button>
                <a href="{{ route('admin.login.activity') }}" class="tf-button style-2">Reset</a>
            </form>
        </div>

        <div class="wg-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Email</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td style="white-space:nowrap;">{{ $log->created_at->format('d M Y h:i:s A') }}</td>
                            <td>{{ $log->email }}</td>
                            <td>{{ $log->user?->name ?? '—' }}</td>
                            <td>
                                @if($log->action === 'login')
                                    <span class="badge bg-success">Login</span>
                                @elseif($log->action === 'logout')
                                    <span class="badge bg-secondary">Logout</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td><code>{{ $log->ip_address }}</code></td>
                            <td><small style="color:#888;">{{ Str::limit($log->user_agent, 60) }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-20">No activity found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-20">{{ $logs->links() }}</div>
        </div>
    </div>
</div>
@endsection
