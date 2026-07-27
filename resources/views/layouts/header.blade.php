<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        {{-- Dynamic Home Link based on Role --}}
        <li class="nav-item d-none d-md-inline-block">
            @role('SuperAdmin')
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link">SuperAdmin Dashboard</a>
            @endrole
            @role('SchoolAdmin')
                <a href="{{ route('schooladmin.dashboard') }}" class="nav-link">School Dashboard</a>
            @endrole
            @role('Teacher')
                <a href="{{ route('teacher.dashboard') }}" class="nav-link">My Classroom</a>
            @endrole
            @role('Student')
                <a href="{{ route('student.dashboard') }}" class="nav-link">Student Portal</a>
            @endrole
            @role('Parent')
                <a href="{{ route('parent.dashboard') }}" class="nav-link">Family Portal</a>
            @endrole
        </li>

        {{-- Mobile home icon --}}
        <li class="nav-item d-md-none">
            @php
                $mobileDashboardRoute = '';
                if (auth()->user()->hasRole('SuperAdmin')) $mobileDashboardRoute = 'superadmin.dashboard';
                elseif (auth()->user()->hasRole('SchoolAdmin')) $mobileDashboardRoute = 'schooladmin.dashboard';
                elseif (auth()->user()->hasRole('Teacher')) $mobileDashboardRoute = 'teacher.dashboard';
                elseif (auth()->user()->hasRole('Student')) $mobileDashboardRoute = 'student.dashboard';
                elseif (auth()->user()->hasRole('Parent')) $mobileDashboardRoute = 'parent.dashboard';
            @endphp
            @if ($mobileDashboardRoute)
                <a href="{{ route($mobileDashboardRoute) }}" class="nav-link"><i class="fas fa-home"></i></a>
            @endif
        </li>

        {{-- Add inside the navbar ul.navbar-nav --}}
        @hasanyrole('SchoolAdmin')
            @unless(request()->routeIs('subject.*', 'section.*', 'classLevel.*', 'teachers.*', 'student.*', 'parents.*', 'invoices.*', 'search.*'))
                <li class="nav-item d-none d-sm-block">
                    <div class="navbar-search-block" style="display:block;position: relative;">
                        <div class="input-group input-group-sm" style="position: relative;">
                            <input type="text" id="globalSearchInput" class="form-control"
                                placeholder="Search students, teachers..."
                                style="border-radius: 20px; padding-right: 38px; min-width: 160px;"
                                autocomplete="off"
                                data-search-url="{{ route('search.live') }}"
                                data-search-index-url="{{ route('search.index') }}">
                            <button type="button" class="btn btn-sm"
                                onclick="submitGlobalSearch()"
                                style="position: absolute; right: 2px; top: 50%; transform: translateY(-50%); border-radius: 50%; border: none; background: transparent; z-index: 5; padding: 4px 8px;">
                                <i class="fas fa-search text-secondary"></i>
                            </button>
                        </div>

                        {{-- Live dropdown results --}}
                        <div id="searchDropdown" class="dropdown-menu shadow-lg"
                            style="display: none; position: absolute; top: 100%; left: 0; z-index: 9999; min-width: 100%; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                        </div>
                    </div>
                </li>
            @endunless
                <li class="nav-item d-sm-none">
                    <a class="nav-link" href="{{ route('search.index') }}" title="Search">
                        <i class="fas fa-search"></i>
                    </a>
                </li>
        @endhasanyrole
    </ul>

    <ul class="navbar-nav ml-auto">
        @auth
            @if (session('active_school'))
                @php
                    $activeSession = \App\Models\AcademicSession::where('school_id', session('active_school'))
                        ->where('is_active', true)
                        ->first();
                    $activeTerm = \App\Models\Term::where('school_id', session('active_school'))
                        ->where('is_active', true)
                        ->first();
                @endphp

                <li class="nav-item d-none d-md-flex align-items-center mr-3">
                    @if ($activeSession && $activeTerm)
                        <span class="badge badge-success px-3 py-2 text-sm elevation-1" style="border-radius: 20px;">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $activeSession->name }} &nbsp;|&nbsp; {{ $activeTerm->name }}
                        </span>
                    @else
                        <span class="badge badge-danger px-3 py-2 text-sm elevation-1" style="border-radius: 20px;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> No Active Term Setup
                        </span>
                    @endif
                </li>
                {{-- Mobile session indicator --}}
                <li class="nav-item d-md-none mr-2">
                    @if ($activeSession && $activeTerm)
                        <span class="badge badge-success p-1" style="border-radius: 50%; width: 26px; height: 26px; line-height: 16px; text-align: center;" title="{{ $activeSession->name }} | {{ $activeTerm->name }}">
                            <i class="fas fa-calendar-alt" style="font-size: 11px;"></i>
                        </span>
                    @else
                        <span class="badge badge-danger p-1" style="border-radius: 50%; width: 26px; height: 26px; line-height: 16px; text-align: center;" title="No Active Term Setup">
                            <i class="fas fa-exclamation-triangle" style="font-size: 11px;"></i>
                        </span>
                    @endif
                </li>
            @endif

            @role('Teacher')
                <li class="nav-item dropdown">
                    <a class="nav-link bg-primary rounded px-2 px-md-3 mr-2 text-white py-md-1" data-toggle="dropdown" href="#"
                        style="margin-top: 4px;">
                        <i class="fas fa-plus mr-md-1"></i><span class="d-none d-md-inline"> Quick Add</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm" style="max-width: 90vw;">
                        <span class="dropdown-header">Classroom Actions</span>
                        <div class="dropdown-divider"></div>

                        <a href="#" class="dropdown-item">
                            <i class="fas fa-tasks mr-2 text-primary"></i> New Assignment
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file-pdf mr-2 text-danger"></i> Upload Material
                        </a>
                        <a href="{{ route('teacher.announcements.index') }}" class="dropdown-item">
                            <i class="fas fa-bullhorn mr-2 text-warning"></i> Post Announcement
                        </a>

                        <div class="dropdown-divider"></div>
                        <a href="{{ route('teacher.messages.index') }}" class="dropdown-item">
                            <i class="fas fa-envelope mr-2 text-success"></i> Message Parent
                        </a>
                    </div>
                </li>
            @endrole


            @auth
                <li class="nav-item dropdown">
                    @php
                        $unreadCount = auth()->user()->unreadNotifications->count();
                        $recentNotifications = auth()->user()->unreadNotifications->take(5);
                    @endphp

                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        @if ($unreadCount > 0)
                            <span class="badge badge-danger navbar-badge">{{ $unreadCount }}</span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-width: 90vw;">
                        <span class="dropdown-item dropdown-header">
                            {{ $unreadCount }} Unread Notification{{ $unreadCount != 1 ? 's' : '' }}
                        </span>
                        <div class="dropdown-divider"></div>

                        @forelse($recentNotifications as $notification)
                            @php $data = $notification->data; @endphp
                            <a href="{{ $data['url'] ?? '#' }}" class="dropdown-item"
                                onclick="markRead('{{ $notification->id }}', event)">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $data['color'] ?? 'secondary' }} mr-2 p-2"
                                        style="min-width: 32px; text-align: center;">
                                        <i class="{{ $data['icon'] ?? 'fas fa-bell' }}"></i>
                                    </span>
                                    <div style="min-width: 0;">
                                        <div class="font-weight-bold text-sm text-truncate">
                                            {{ $data['title'] ?? 'Notification' }}
                                        </div>
                                        <small class="text-muted text-truncate d-block">
                                            {{ Str::limit($data['message'] ?? '', 50) }}
                                        </small>
                                        <small class="text-muted">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                        @empty
                            <div class="dropdown-item text-center text-muted py-3">
                                <i class="fas fa-bell-slash mr-1"></i> No new notifications
                            </div>
                        @endforelse

                        @if ($unreadCount > 0)
                            <a href="#" class="dropdown-item dropdown-footer text-center" onclick="markAllRead(event)">
                                <i class="fas fa-check-double mr-1"></i> Mark all as read
                            </a>
                        @endif
                    </div>
                </li>
            @endauth

        @endauth

        @auth
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('dist/img/user2-160x160.jpg') }}" class="user-image img-circle elevation-2"
                        alt="User Image">
                    <span class="d-none d-md-inline">{{ auth()->user()?->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-width: 90vw;">
                    <li class="user-header bg-primary">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                            alt="User Image">
                        <p>
                            {{ auth()->user()?->name }}
                            <small>{{ ucwords(str_replace('_', ' ', auth()->user()?->roles->first()->name ?? 'User')) }}</small>
                            @if (session('active_school'))
                                <small
                                    class="d-block mt-1">{{ \App\Models\School::find(session('active_school'))->name }}</small>
                            @endif
                        </p>
                    </li>
                    <li class="user-footer">
                        @role('Teacher')
                            <a href="{{ route('teacher.profile') }}" class="btn btn-default btn-flat">Profile</a>
                        @elserole('SchoolAdmin')
                            <a href="{{ route('school.profile') }}" class="btn btn-default btn-flat">Profile</a>
                        @else
                            <a href="#" class="btn btn-default btn-flat">Profile</a>
                        @endrole
                        <form action="{{ route('logout') }}" method="POST" class="float-right">
                            @csrf
                            <button type="submit" class="btn btn-default btn-flat">Sign out</button>
                        </form>
                    </li>
                </ul>
            </li>
        @endauth
    </ul>
</nav>

@auth
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <div class="sidebar">
            <div class="text-center pt-3 pb-2 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                @role('SuperAdmin')
                    <img src="{{ asset('dist/img/axia-logo.svg') }}" alt="Axia SMS"
                        style="width: 45px; height: 45px; object-fit: contain; opacity: .8;">
                    <div class="font-weight-bold text-white mt-1" style="font-size: 0.9rem; line-height: 1.2;">
                        Axia SMS
                    </div>
                @else
                    @php $school = \App\Models\School::find(session('active_school')); @endphp
                    <img src="{{ $school?->logo ? asset('storage/' . $school->logo) : asset('dist/img/axia-logo.svg') }}"
                        alt="School Logo"
                        style="width: 45px; height: 45px; object-fit: contain; opacity: .8;">
                    <div class="font-weight-bold text-white mt-1" style="font-size: 0.9rem; line-height: 1.2; word-break: break-word;">
                        {{ $school->name ?? 'My School' }}
                    </div>
                @endrole
                <div class="text-muted small mt-1" style="font-size: 0.75rem; line-height: 1.2;">
                    {{ auth()->user()->name }}
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

                    <li class="nav-item">
                        @role('SuperAdmin')
                            <a href="{{ route('superadmin.dashboard') }}" class="nav-link @activeRoute('superadmin.dashboard')"><i
                                    class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        @endrole
                        @role('SchoolAdmin')
                            <a href="{{ route('schooladmin.dashboard') }}" class="nav-link @activeRoute('schooladmin.dashboard')"><i
                                    class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        @endrole
                        @role('Teacher')
                            <a href="{{ route('teacher.dashboard') }}" class="nav-link @activeRoute('teacher.dashboard')"><i
                                    class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>My Dashboard</p>
                            </a>
                        @endrole
                        @role('Student')
                            <a href="{{ route('student.dashboard') }}" class="nav-link @activeRoute('student.dashboard')"><i
                                    class="nav-icon fas fa-user-graduate"></i>
                                <p>Student Portal</p>
                            </a>
                        @endrole
                        @role('Parent')
                            <a href="{{ route('parent.dashboard') }}" class="nav-link @activeRoute('parent.dashboard')"><i
                                    class="nav-icon fas fa-user-friends"></i>
                                <p>Parent Portal</p>
                            </a>
                        @endrole
                    </li>

                    @role('SuperAdmin')
                        <li class="nav-item">
                            <a href="{{ route('school.index') }}" class="nav-link @activeRoute('school.*')"><i
                                    class="nav-icon fas fa-school"></i>
                                <p>Schools</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('superadmin.admins.index') }}" class="nav-link @activeRoute('superadmin.admins.*')">
                                <i class="nav-icon fas fa-user-shield"></i>
                                <p>School Admins</p>
                            </a>
                        </li>
                    @endrole

                    @role('SchoolAdmin')
                        <li class="nav-header">ACADEMICS</li>
                        <li class="nav-item"><a href="{{ route('classLevel.index') }}" class="nav-link @activeRoute('classLevel.*')"><i
                                    class="nav-icon fas fa-layer-group"></i>
                                <p>Classes / Grades</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('section.index') }}" class="nav-link @activeRoute('section.*')"><i
                                    class="nav-icon fas fa-puzzle-piece"></i>
                                <p>Sections</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('subject.index') }}" class="nav-link @activeRoute('subject.*')"><i
                                    class="nav-icon fas fa-book"></i>
                                <p>Subjects</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.timetable.index') }}"
                                class="nav-link @activeRoute('timetable.*')"><i class="nav-icon fas fa-calendar-week"></i>
                                <p>Timetable</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.attendance.index') }}"
                                class="nav-link @activeRoute('attendance.*')"><i class="nav-icon fas fa-user-check"></i>
                                <p>Attendance</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.grades.index') }}"
                                class="nav-link @activeRoute('grades.*')"><i class="nav-icon fas fa-graduation-cap"></i>
                                <p>Grade</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.assessments.index') }}"
                                class="nav-link @activeRoute('assessments.*')"><i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Assessment</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.reports.index') }}"
                                class="nav-link @activeRoute('reports.*')"><i class="nav-icon fas fa-file-contract"></i>
                                <p>Report Cards</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('academic-settings.index') }}"
                                class="nav-link @activeRoute('academic-settings.*')"><i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Academic Settings</p>
                            </a></li>

                        <li class="nav-header">PEOPLE</li>
                        <li class="nav-item">
                            <a href="{{ route('teachers.index') }}" class="nav-link @activeRoute('teachers.*')">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>Teachers <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('teachers.index') }}" class="nav-link @activeRoute('teachers.index', 'teachers.edit')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Teachers</p>
                                    </a>
                                </li>
                                <li class="nav-item"><a href="{{ route('teachers.create') }}"
                                        class="nav-link @activeRoute('teachers.create')"><i class="far fa-circle nav-icon"></i>
                                        <p>Add Teachers</p>
                                    </a></li>
                                <li class="nav-item"><a href="{{ route('teachers.assignments') }}"
                                        class="nav-link @activeRoute('teachers.assignments')"><i class="far fa-circle nav-icon"></i>
                                        <p>Assignments</p>
                                    </a></li>
                            </ul>
                        </li>
                        <li class="nav-item @menuOpen('student.*')">
                            <a href="{{ route('student.index') }}" class="nav-link @activeRoute('student*')"><i
                                    class="nav-icon fas fa-user-graduate"></i>
                                <p>Students <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="{{ route('student.index') }}"
                                        class="nav-link @activeRoute('student.index')"><i class="far fa-circle nav-icon"></i>
                                        <p>All Students</p>
                                    </a></li>
                                <li class="nav-item"><a href="{{ route('student.create') }}"
                                        class="nav-link @activeRoute('student.create')"><i class="far fa-circle nav-icon"></i>
                                        <p>Admission</p>
                                    </a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('parents.index') }}" class="nav-link @activeRoute('parents.*')">
                                <i class="nav-icon fas fa-user-friends"></i>
                                <p>Parents</p>
                            </a>
                        </li>

                        <li class="nav-header">FINANCE</li>
                        <li class="nav-item"><a href="{{ route('fees.index') }}" class="nav-link @activeRoute('fees.*')"><i
                                    class="nav-icon fas fa-coins"></i>
                                <p>Fees</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('invoices.index') }}" class="nav-link @activeRoute('invoices.*')"><i
                                    class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Invoice</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('finance.reports.index') }}"
                                class="nav-link @activeRoute('finance.reports.*')"><i class="nav-icon fas fa-chart-pie"></i>
                                <p>Reports</p>
                            </a></li>
                    @endrole

                    @role('Teacher')
                        <li class="nav-header">CLASSROOM</li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.classes') }}" class="nav-link @activeRoute('teacher.classes')">
                                <i class="nav-icon fas fa-users"></i>
                                <p>My Classes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.grades.index') }}" class="nav-link @activeRoute('teacher.grades.*')">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>Enter Grades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.attendance.index') }}" class="nav-link @activeRoute('teacher.attendance.*')">
                                <i class="nav-icon fas fa-user-check"></i>
                                <p>Take Attendance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.timetable.index') }}" class="nav-link @activeRoute('teacher.timetable.*')">
                                <i class="nav-icon fas fa-calendar-week"></i>
                                <p>My Schedule</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.reports.index') }}" class="nav-link @activeRoute('teacher.reports.*')">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>Report Cards</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.assessments.index') }}" class="nav-link @activeRoute('teacher.assessments.*')">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Assessments</p>
                            </a>
                        </li>
                        <li class="nav-header">MY STUDENTS</li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.students') }}" class="nav-link @activeRoute('teacher.students.*')">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Student Directory</p>
                            </a>
                        </li>

                        <li class="nav-header">PERSONAL</li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.profile') }}" class="nav-link @activeRoute('teacher.profile')">
                                <i class="nav-icon fas fa-user-circle"></i>
                                <p>My Profile</p>
                            </a>
                        </li>
                    @endrole

                    @hasanyrole('SchoolAdmin|Teacher|Student|Parent')
                        <li class="nav-header">COMMUNICATION</li>

                        @hasanyrole('SchoolAdmin|Teacher')
                            <li class="nav-item">
                                <a href="{{ resolveRoute('announcements.index') }}" class="nav-link @activeRoute('admin.announcements.*', 'teacher.announcements.*')">
                                    <i class="nav-icon fas fa-bullhorn"></i>
                                    <p>Announcements</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ resolveRoute('messages.index') }}" class="nav-link @activeRoute('admin.messages.*', 'teacher.messages.*')">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>Messages</p>
                                </a>
                            </li>
                        @endhasanyrole

                        @hasanyrole('Student|Parent')
                            {{-- Build student/parent communication routes when needed --}}
                            <li class="nav-item">
                                <a href="#" class="nav-link disabled" tabindex="-1" aria-disabled="true">
                                    <i class="nav-icon fas fa-bullhorn"></i>
                                    <p>Announcements</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link disabled" tabindex="-1" aria-disabled="true">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>Messages</p>
                                </a>
                            </li>
                        @endhasanyrole
                    @endhasanyrole

                    @role('SchoolAdmin')
                        <li class="nav-header">SETTINGS</li>
                        <li class="nav-item"><a href="{{ route('school.profile') }}" class="nav-link @activeRoute('school.profile')"><i
                                     class="nav-icon fas fa-cogs"></i>
                                 <p>School Profile</p>
                             </a>
                         </li>

                        <li class="nav-item">
                            <a href="{{ route('search.index') }}" class="nav-link @activeRoute('search.*')">
                                <i class="nav-icon fas fa-search"></i>
                                <p>Search</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notification.preferences') }}" class="nav-link @activeRoute('notification.preferences')">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    @endrole

                </ul>
            </nav>
        </div>
    </aside>
@endauth
