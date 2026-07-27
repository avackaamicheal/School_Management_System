@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 mb-2">
                    <h1 class="m-0">Parents / Guardians</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('parents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Parent
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
                            All Parents
                            @if(request('search'))
                                <span class="badge badge-info ml-2">{{ $parents->total() }} found</span>
                            @else
                                <span class="badge badge-secondary ml-2">{{ $parents->total() }}</span>
                            @endif
                        </h3>
                        <x-search-filter
                            :route="route('parents.index')"
                            placeholder="Search by name, email or phone..."
                        />
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Parent</th>
                                <th>Contact</th>
                                <th>Children</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parents as $parent)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $parent->name }}</div>
                                        <small class="text-muted">
                                            {{ $parent->parentProfile->occupation ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div>{{ $parent->email }}</div>
                                        <small class="text-muted">
                                            {{ $parent->parentProfile->alt_phone ?? 'No phone' }}
                                        </small>
                                    </td>
                                    <td>
                                        @forelse($parent->children as $child)
                                            <span class="badge badge-info mb-1">
                                                {{ $child->name }}
                                                ({{ $child->studentProfile->section->classLevel->name ?? 'N/A' }})
                                            </span><br>
                                        @empty
                                            <span class="text-muted">No children linked</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('parents.show', $parent->id) }}"
                                                class="btn btn-sm btn-info py-1 px-2 mx-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <a href="{{ route('parents.edit', $parent->id) }}"
                                            class="btn btn-sm btn-warning mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('parents.destroy', $parent->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete {{ $parent->name }}? This cannot be undone.')">
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
                                            No parents found matching "{{ request('search') }}".
                                            <a href="{{ route('parents.index') }}">Clear search</a>
                                        @else
                                            <i class="fas fa-user-friends fa-2x mb-2"></i><br>
                                            No parents added yet.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="card-footer clearfix">
                    {{ $parents->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
