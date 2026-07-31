@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">All Notifications</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Notification History</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px"></th>
                                        <th>Notification</th>
                                        <th class="d-none d-md-table-cell" style="width: 160px">Date</th>
                                        <th style="width: 100px">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($notifications as $notification)
                                        @php $data = $notification->data; @endphp
                                        <tr class="{{ $notification->read_at ? '' : 'font-weight-bold' }}">
                                            <td class="text-center align-middle">
                                                <span class="badge badge-{{ $data['color'] ?? 'secondary' }} p-2"
                                                    style="min-width: 32px;">
                                                    <i class="{{ $data['icon'] ?? 'fas fa-bell' }}"></i>
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ $data['url'] ?? '#' }}" class="text-dark d-block">
                                                    <span>{{ $data['title'] ?? 'Notification' }}</span>
                                                    <small class="text-muted d-block">
                                                        {{ Str::limit($data['message'] ?? '', 80) }}
                                                    </small>
                                                </a>
                                            </td>
                                            <td class="align-middle text-muted small d-none d-md-table-cell">
                                                {{ $notification->created_at->format('M d, Y h:i A') }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($notification->read_at)
                                                    <span class="badge badge-secondary">Read</span>
                                                @else
                                                    <span class="badge badge-primary">Unread</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                                                No notifications yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($notifications->hasPages())
                        <div class="card-footer clearfix">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection