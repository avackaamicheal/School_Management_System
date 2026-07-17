<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">

    <style type="text/css">
        .required {
            color: red;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    @auth
        @if (auth()->user()->hasRole('SchoolAdmin'))
            @php
                $sub = auth()->user()->school?->activeSubscription()->first();
            @endphp
            @if ($sub && $sub->daysUntilExpiry() <= 7 && $sub->daysUntilExpiry() > 0)
                <div class="alert alert-warning text-center mb-0 py-2" style="border-radius: 0;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Your subscription expires in <strong>{{ $sub->daysUntilExpiry() }} days</strong>
                    on {{ $sub->expires_at->format('M d, Y') }}.
                    <a href="{{ route('subscription.index') }}" class="font-weight-bold ml-2">
                        Renew Now
                    </a>
                </div>
            @endif

            @if ($sub && $sub->isInGracePeriod())
                <div class="alert alert-danger text-center mb-0 py-2" style="border-radius: 0;">
                    <i class="fas fa-lock mr-1"></i>
                    Your subscription has expired. You are in a grace period.
                    <a href="{{ route('subscription.index') }}" class="font-weight-bold text-white ml-2">
                        Renew Now to avoid losing access
                    </a>
                </div>
            @endif
        @endif
    @endauth
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo"
                height="60" width="60">
        </div>

        @include('layouts.header')

        <div>
            @include('partials.alert') {{-- This handles standard Session flashes --}}

            @yield('content')
        </div>

        <!-- /.content-wrapper -->
        @include('layouts.footer')

        <!-- Control Sidebar -->

        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    {{-- <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script> --}}

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
    {{-- <script src="{{ asset('dist/js/adminlte.min.js') }}"></script> --}}

    <script>
        window.showFlash = function(type, message) {
            const container = document.getElementById('ajax-alert-container');
            if (!container) {
                console.error("Alert container missing! Add <div id='ajax-alert-container'></div> to your layout.");
                return;
            }

            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check' : 'fa-ban';

            const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas ${icon} mr-1"></i> ${message}
            </div>
        `;

            container.innerHTML = alertHtml;

            // Auto-hide the alert after 4 seconds
            setTimeout(() => {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 4000);
        };
    </script>

    @stack('scripts')

    @hasanyrole('SchoolAdmin')
        <script>
            const searchInput = document.getElementById('globalSearchInput');
            const dropdown = document.getElementById('searchDropdown');
            let searchTimeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        dropdown.style.display = 'none';
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route('search.live') }}?q=${encodeURIComponent(query)}`)
                            .then(res => res.json())
                            .then(results => {
                                if (results.length === 0) {
                                    dropdown.innerHTML = `
                                <div class="dropdown-item text-muted text-center py-3">
                                    No results found for "${query}"
                                </div>`;
                                } else {
                                    dropdown.innerHTML = results.map(item => `
                                <a href="${item.url}" class="dropdown-item py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-${item.color} mr-3 p-2">
                                            <i class="${item.icon}"></i>
                                        </span>
                                        <div>
                                            <div class="font-weight-bold text-sm">${item.title}</div>
                                            <small class="text-muted">${item.subtitle} — ${item.meta}</small>
                                        </div>
                                        <span class="badge badge-light ml-auto">${item.type}</span>
                                    </div>
                                </a>
                            `).join('') + `
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('search.index') }}?q=${encodeURIComponent(query)}"
                                    class="dropdown-item text-center text-primary font-weight-bold">
                                    <i class="fas fa-search mr-1"></i>
                                    View all results for "${query}"
                                </a>
                            `;
                                }
                                dropdown.style.display = 'block';
                            })
                            .catch(() => {
                                dropdown.style.display = 'none';
                            });
                    }, 300);
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = 'none';
                    }
                });

                // Submit on Enter
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        submitGlobalSearch();
                    }
                });
            }

            function submitGlobalSearch() {
                const query = document.getElementById('globalSearchInput').value.trim();
                if (query) {
                    window.location.href = `{{ route('search.index') }}?q=${encodeURIComponent(query)}`;
                }
            }
        </script>
    @endhasanyrole

    <script>
        function markRead(id, event) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });
            // Don't prevent default — let the link navigate
        }

        function markAllRead(event) {
            event.preventDefault();
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            }).then(() => location.reload());
        }
    </script>
</body>

</html>
