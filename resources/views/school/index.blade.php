@extends('layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>School List</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('school.create') }}" class="btn btn-primary">Add School</a>
                    </div>
                    {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">School</li>
                        </ol>
                    </div> --}}
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- /.col -->
                    <div class="col-md-12">

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                    <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>School Name</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Principal Name</th>
                                            <th>Phone Number</th>
                                            <th>Status</th>
                                            <th>Subscription</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schools as $school)
                                            <tr>
                                                <td>{{ $school->id }}</td>
                                                <td>{{ $school->name }}</td>
                                                <td>{{ $school->email }}</td>
                                                <td>{{ $school->address }}</td>
                                                <td>{{ $school->principal_name }}</td>
                                                <td>{{ $school->phone_number }}</td>
                                                <td class="align-middle">
                                                    @if ($school->approval_status === 'rejected')
                                                        <span class="badge badge-danger">Deactivated</span>
                                                    @elseif($school->is_active && $school->hasActiveSubscription())
                                                        <span class="badge badge-success">Active</span>
                                                    @elseif($school->approval_status === 'pending')
                                                        <span class="badge badge-warning">Awaiting Payment</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                {{-- Subscription column --}}
                                                <td class="align-middle">
                                                    @php $sub = $school->activeSubscription()->with('plan')->first(); @endphp
                                                    @if ($sub && $sub->isActive())
                                                        <span class="badge badge-success p-2">
                                                            {{ $sub->plan->name }} |
                                                            Expires {{ $sub->expires_at->format('M d, Y') }}
                                                        </span>
                                                    @elseif($sub && $sub->isInGracePeriod())
                                                        <span class="badge badge-warning p-2">
                                                            Grace Period —
                                                            {{ 7 - (int) $sub->expires_at->diffInDays(now()) }} days left
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">No Subscription</span>
                                                    @endif
                                                </td>

                                                {{-- Actions column --}}
                                                <td class="align-middle text-right">
                                                    <a href="{{ route('school.edit', $school->slug) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>

                                                    @if ($school->approval_status !== 'rejected')
                                                        <form action="{{ route('school.deactivate', $school->slug) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Deactivate {{ $school->name }}?')">
                                                                <i class="fas fa-ban"></i> Deactivate
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('school.reactivate', $school->slug) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                onclick="return confirm('Reactivate {{ $school->name }}?')">
                                                                <i class="fas fa-check"></i> Reactivate
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('school.destroy', $school->slug) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Permanently delete {{ $school->name }}?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    {{-- <form method="POST" action="{{ route('school.destroy', $school->id) }}" id="delete-form" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                    </form> --}}
                                </table>
                                    </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
