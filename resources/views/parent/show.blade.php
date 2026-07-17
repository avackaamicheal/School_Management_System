@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Parent Profile</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('parents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Parents
                        </a>
                        <a href="{{ route('parents.edit', $parent->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    {{-- Left: Parent Info --}}
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-body text-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="width: 80px; height: 80px; font-size: 2em; font-weight: bold;">
                                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                                </div>
                                <h4 class="font-weight-bold">{{ $parent->name }}</h4>
                                <p class="text-muted">{{ $parent->parentProfile->occupation ?? 'No occupation listed' }}</p>
                            </div>
                            <div class="card-body border-top">
                                <p><i class="fas fa-envelope mr-2 text-muted"></i> {{ $parent->email }}</p>
                                <p><i class="fas fa-phone mr-2 text-muted"></i>
                                    {{ $parent->parentProfile->alt_phone ?? 'No phone listed' }}
                                </p>
                                <p class="mb-0"><i class="fas fa-map-marker-alt mr-2 text-muted"></i>
                                    {{ $parent->parentProfile->address ?? 'No address listed' }}
                                </p>
                            </div>
                        </div>

                        <div class="card card-outline card-info">
                            <div class="card-body text-center">
                                <h3 class="font-weight-bold mb-0">{{ $parent->children->count() }}</h3>
                                <small class="text-muted">Children Enrolled</small>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Children --}}
                    <div class="col-md-8">
                        <div class="card card-outline card-success">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-child mr-1 text-success"></i> Linked Children
                                </h3>
                                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#linkChildModal">
                                    <i class="fas fa-link"></i> Link a Child
                                </button>
                            </div>
                            <div class="card-body p-0">
                                @forelse($parent->children as $child)
                                    @php
                                        $totalBilled = $child->invoices->sum('total_amount');
                                        $totalPaid = $child->invoices->sum('payments_sum_amount');
                                        $balance = $totalBilled - $totalPaid;
                                        $relationship = $child->pivot->relationship ?? 'N/A';
                                    @endphp
                                    <div class="p-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="font-weight-bold mb-1">
                                                    {{ $child->name }}
                                                    <span class="badge badge-secondary ml-1">{{ $relationship }}</span>
                                                </h5>
                                                <small class="text-muted">
                                                    {{ $child->studentProfile->admission_number ?? 'N/A' }} —
                                                    {{ $child->studentProfile->section->classLevel->name ?? 'N/A' }}
                                                    {{ $child->studentProfile->section->name ?? '' }}
                                                </small>
                                                <div class="mt-2">
                                                    @if ($balance > 0)
                                                        <span class="badge badge-danger">
                                                            ₦{{ number_format($balance) }} outstanding
                                                        </span>
                                                    @else
                                                        <span class="badge badge-success">Fees cleared</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <form action="{{ route('parents.unlink-child', [$parent->id, $child->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Unlink {{ $child->name }} from this parent?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-unlink"></i> Unlink
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-5 text-muted">
                                        <i class="fas fa-child fa-2x mb-2"></i>
                                        <p>No children linked yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- Link Child Modal --}}
    <div class="modal fade" id="linkChildModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('parents.link-child', $parent->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Link a Child to {{ $parent->name }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Search Student</label>
                            <input type="text" id="studentSearchInput" class="form-control"
                                placeholder="Type name or admission number..." autocomplete="off">
                            <div id="studentSearchResults" class="list-group mt-2"
                                style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div id="selectedStudentBox" class="alert alert-info d-none">
                            Selected: <strong id="selectedStudentName"></strong>
                            <input type="hidden" name="student_id" id="selectedStudentId">
                        </div>

                        <div class="form-group">
                            <label>Relationship <span class="text-danger">*</span></label>
                            <select name="relationship" class="form-control" required>
                                <option value="">-- Select --</option>
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Guardian">Guardian</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="linkSubmitBtn" disabled>
                            <i class="fas fa-link"></i> Link Child
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('studentSearchInput');
            const resultsBox = document.getElementById('studentSearchResults');
            const selectedBox = document.getElementById('selectedStudentBox');
            const selectedName = document.getElementById('selectedStudentName');
            const selectedId = document.getElementById('selectedStudentId');
            const submitBtn = document.getElementById('linkSubmitBtn');

            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    resultsBox.innerHTML = '';
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`{{ route('parents.search-students') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(students => {
                            if (students.length === 0) {
                                resultsBox.innerHTML = `
                            <div class="list-group-item text-muted text-center">
                                No students found.
                            </div>`;
                                return;
                            }
                            resultsBox.innerHTML = students.map(s => `
                        <a href="#" class="list-group-item list-group-item-action student-option"
                            data-id="${s.id}" data-name="${s.name}">
                            <strong>${s.name}</strong> — ${s.admission_number}
                            <br><small class="text-muted">${s.class}</small>
                        </a>
                    `).join('');

                            document.querySelectorAll('.student-option').forEach(option => {
                                option.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    selectedId.value = this.dataset.id;
                                    selectedName.textContent = this.dataset
                                    .name;
                                    selectedBox.classList.remove('d-none');
                                    resultsBox.innerHTML = '';
                                    searchInput.value = '';
                                    submitBtn.disabled = false;
                                });
                            });
                        });
                }, 300);
            });
        });
    </script>
@endsection
