@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Parents / Guardians</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('parents.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Parent
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        All Parents
                        @if(request('search'))
                            <span class="badge badge-info ml-2">{{ $parents->total() }} found</span>
                        @else
                            <span class="badge badge-secondary ml-2">{{ $parents->total() }} total</span>
                        @endif
                    </h3>
                    <x-search-filter
                        :route="route('parents.index')"
                        placeholder="Search by name, email or phone..."
                    />
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover text-nowrap">
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
                                    <td class="text-right">
                                        <a href="{{ route('parents.show', $parent->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('parents.edit', $parent->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
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

                <div class="card-footer clearfix">
                    {{ $parents->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
