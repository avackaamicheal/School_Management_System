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
            <div id="ajax-alert-container"
                style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>

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
                <h5><i class="icon fas ${icon}"></i> ${type === 'success' ? 'Success' : 'Error'}</h5>
                ${message}
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
</body>

</html>
