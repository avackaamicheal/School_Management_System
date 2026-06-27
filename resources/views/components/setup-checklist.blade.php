@if (!$isComplete)
    <div class="card card-outline card-warning shadow-sm mb-4" id="setupChecklistCard">
        <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;"
            onclick="toggleSetupChecklist()">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-rocket mr-2 text-warning"></i>
                Finish Setting Up Your School
                @php
                    $completedCount = count(array_filter($status));
                @endphp
                <span class="badge badge-warning ml-2">{{ $completedCount }} / 5 complete</span>
            </h3>
            <i class="fas fa-chevron-down" id="setupChevron"></i>
        </div>

        <div class="card-body" id="setupChecklistBody">
            <p class="text-muted mb-3">
                Complete these steps to unlock the full power of your dashboard.
            </p>

            <div class="row">

                {{-- Step 1: Academic Session --}}
                <div class="col-md-12 mb-2">
                    <div
                        class="d-flex align-items-center justify-content-between p-3 rounded
                    {{ $status['has_session'] ? 'bg-light' : 'bg-white border border-warning' }}">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $status['has_session'] ? 'success' : 'secondary' }} mr-3 p-2">
                                <i class="fas fa-{{ $status['has_session'] ? 'check' : '1' }}"></i>
                            </span>
                            <div>
                                <div class="font-weight-bold {{ $status['has_session'] ? 'text-muted' : '' }}">
                                    Create Academic Session
                                </div>
                                <small class="text-muted">
                                    e.g. 2025/2026 — the academic year for your school
                                </small>
                            </div>
                        </div>
                        @if (!$status['has_session'])
                            <a href="{{ route('academic-settings.index') }}" class="btn btn-sm btn-warning">
                                Set Up <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @else
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        @endif
                    </div>
                </div>

                {{-- Step 2: Term --}}
                <div class="col-md-12 mb-2">
                    <div
                        class="d-flex align-items-center justify-content-between p-3 rounded
                    {{ $status['has_term'] ? 'bg-light' : 'bg-white border border-warning' }}">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $status['has_term'] ? 'success' : 'secondary' }} mr-3 p-2">
                                <i class="fas fa-{{ $status['has_term'] ? 'check' : '2' }}"></i>
                            </span>
                            <div>
                                <div class="font-weight-bold {{ $status['has_term'] ? 'text-muted' : '' }}">
                                    Create a Term
                                </div>
                                <small class="text-muted">
                                    e.g. First Term — terms belong to an academic session
                                </small>
                            </div>
                        </div>
                        @if (!$status['has_term'])
                            <a href="{{ route('academic-settings.index') }}" class="btn btn-sm btn-warning">
                                Set Up <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @else
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Classes --}}
                <div class="col-md-12 mb-2">
                    <div
                        class="d-flex align-items-center justify-content-between p-3 rounded
                    {{ $status['has_class'] ? 'bg-light' : 'bg-white border border-warning' }}">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $status['has_class'] ? 'success' : 'secondary' }} mr-3 p-2">
                                <i class="fas fa-{{ $status['has_class'] ? 'check' : '3' }}"></i>
                            </span>
                            <div>
                                <div class="font-weight-bold {{ $status['has_class'] ? 'text-muted' : '' }}">
                                    Set Up Classes
                                </div>
                                <small class="text-muted">
                                    e.g. JSS1, JSS2 — or use a quick preset structure
                                </small>
                            </div>
                        </div>
                        @if (!$status['has_class'])
                            <a href="{{ route('quick-setup.show') }}" class="btn btn-sm btn-warning">
                                Quick Setup <i class="fas fa-magic ml-1"></i>
                            </a>
                        @else
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        @endif
                    </div>
                </div>

                {{-- Step 4: Sections --}}
                <div class="col-md-12 mb-2">
                    <div
                        class="d-flex align-items-center justify-content-between p-3 rounded
                    {{ $status['has_section'] ? 'bg-light' : 'bg-white border border-warning' }}">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $status['has_section'] ? 'success' : 'secondary' }} mr-3 p-2">
                                <i class="fas fa-{{ $status['has_section'] ? 'check' : '4' }}"></i>
                            </span>
                            <div>
                                <div class="font-weight-bold {{ $status['has_section'] ? 'text-muted' : '' }}">
                                    Set Up Sections
                                </div>
                                <small class="text-muted">
                                    e.g. JSS1 A — students get assigned to a section
                                </small>
                            </div>
                        </div>
                        @if (!$status['has_section'])
                            <a href="{{ route('section.index') }}" class="btn btn-sm btn-warning">
                                Set Up <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @else
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        @endif
                    </div>
                </div>

                {{-- Step 5: Subjects --}}
                <div class="col-md-12 mb-2">
                    <div
                        class="d-flex align-items-center justify-content-between p-3 rounded
                    {{ $status['has_subject'] ? 'bg-light' : 'bg-white border border-warning' }}">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-{{ $status['has_subject'] ? 'success' : 'secondary' }} mr-3 p-2">
                                <i class="fas fa-{{ $status['has_subject'] ? 'check' : '5' }}"></i>
                            </span>
                            <div>
                                <div class="font-weight-bold {{ $status['has_subject'] ? 'text-muted' : '' }}">
                                    Add Subjects
                                </div>
                                <small class="text-muted">
                                    e.g. Mathematics, English — or use the quick preset
                                </small>
                            </div>
                        </div>
                        @if (!$status['has_subject'])
                            <a href="{{ route('subject.index') }}" class="btn btn-sm btn-warning">
                                Set Up <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @else
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleSetupChecklist() {
            const body = document.getElementById('setupChecklistBody');
            const chevron = document.getElementById('setupChevron');
            const isHidden = body.style.display === 'none';

            body.style.display = isHidden ? 'block' : 'none';
            chevron.classList.toggle('fa-chevron-down', isHidden);
            chevron.classList.toggle('fa-chevron-up', !isHidden);

            // Remember collapsed state for this session only
            sessionStorage.setItem('setupChecklistCollapsed', isHidden ? 'false' : 'true');
        }

        // Restore collapsed state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const collapsed = sessionStorage.getItem('setupChecklistCollapsed');
            if (collapsed === 'true') {
                document.getElementById('setupChecklistBody').style.display = 'none';
                document.getElementById('setupChevron').classList.remove('fa-chevron-down');
                document.getElementById('setupChevron').classList.add('fa-chevron-up');
            }
        });
    </script>
@endif
