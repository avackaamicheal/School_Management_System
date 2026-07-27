@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">SuperAdmin Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalSchools }}</h3>
                                <p>Total Schools</p>
                            </div>
                            <div class="icon"><i class="fas fa-school"></i></div>
                            <a href="{{ route('school.index') }}" class="small-box-footer">
                                Manage Schools <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $pendingApprovals }}</h3>
                                <p>Pending Approvals</p>
                            </div>
                            <div class="icon"><i class="fas fa-clock"></i></div>
                            <a href="{{ route('school.index') }}" class="small-box-footer">
                                Review <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalUsers }}</h3>
                                <p>Total Users</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <a href="{{ route('superadmin.admins.index') }}" class="small-box-footer">
                                View Admins <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $activeSubscriptions }}</h3>
                                <p>Active Subscriptions</p>
                            </div>
                            <div class="icon"><i class="fas fa-credit-card"></i></div>
                            <a href="{{ route('school.index') }}" class="small-box-footer">
                                Details <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <section class="col-lg-7">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-building mr-1"></i> Recent Schools
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover m-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Approval</th>
                                            <th>Registered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSchools as $school)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('school.edit', $school->slug) }}" class="font-weight-bold">
                                                        {{ $school->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $school->email }}</td>
                                                <td>
                                                    @if ($school->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($school->approval_status === 'approved')
                                                        <span class="badge badge-success">Approved</span>
                                                    @elseif($school->approval_status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted">
                                                    <small>{{ $school->created_at->format('M d, Y') }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    No schools registered yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('school.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Schools
                                </a>
                            </div>
                        </div>
                    </section>

                    <section class="col-lg-5">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-1"></i> Subscription Overview
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @forelse($recentSubscriptions as $sub)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $sub->school->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $sub->plan->name ?? 'N/A' }}
                                                    &mdash; expires {{ $sub->expires_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <span class="badge badge-success p-2">
                                                ₦{{ number_format($sub->amount_paid) }}
                                            </span>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center text-muted py-3">
                                            No subscriptions yet.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="card-footer text-center">
                                <strong class="text-success">
                                    Total Revenue: ₦{{ number_format($totalRevenue) }}
                                </strong>
                            </div>
                        </div>

                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-link mr-1"></i> Quick Links
                                </h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('school.create') }}" class="btn btn-block btn-outline-primary mb-2">
                                     Add New School
                                </a>
                                <a href="{{ route('superadmin.admins.index') }}" class="btn btn-block btn-outline-success mb-2">
                                    Manage School Admins
                                </a>
                                <a href="{{ route('school.index') }}" class="btn btn-block btn-outline-info">
                                    Browse All Schools
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
