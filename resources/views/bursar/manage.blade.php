@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 mb-2">
                    <h1 class="m-0">Bursars</h1>
                </div>
                <div class="col-sm-6 text-left text-md-right">
                    <a href="{{ route('bursars.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Bursar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-nowrap">
                        <h3 class="card-title">
                            All Bursars
                            @if(request('search'))
                                <span class="badge badge-info ml-2">{{ $bursars->total() }} found</span>
                            @else
                                <span class="badge badge-secondary ml-2">{{ $bursars->total() }}</span>
                            @endif
                        </h3>
                        <x-search-filter
                            :route="route('bursars.index')"
                            placeholder="Search by name, email, employee ID or phone..."
                        />
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bursar</th>
                                    <th>Employee ID</th>
                                    <th>Contact</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bursars as $bursar)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $bursar->name }}</div>
                                            <small class="text-muted">
                                                @if ($bursar->bursarProfile?->address)
                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $bursar->bursarProfile->address }}
                                                @else
                                                    No address on file
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if ($bursar->bursarProfile?->employee_id)
                                                <span class="badge badge-primary">{{ $bursar->bursarProfile->employee_id }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $bursar->email }}</div>
                                            <small class="text-muted">{{ $bursar->bursarProfile?->phone ?? 'No phone' }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <a href="{{ route('bursars.edit', $bursar->id) }}"
                                                    class="btn btn-sm btn-warning mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('bursars.destroy', $bursar->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete {{ $bursar->name }}? This cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            @if(request('search'))
                                                <i class="fas fa-search fa-2x mb-2"></i><br>
                                                No bursars found matching "{{ request('search') }}".
                                                <a href="{{ route('bursars.index') }}">Clear search</a>
                                            @else
                                                <i class="fas fa-user-tie fa-2x mb-2"></i><br>
                                                No bursars added yet.
                                                <a href="{{ route('bursars.create') }}">Add your first bursar.</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix">
                    {{ $bursars->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
