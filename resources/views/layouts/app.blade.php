<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Axia SMS | Dashboard')</title>

    @vite(['resources/css/adminlte-overrides.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">

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
            <img class="animation__shake" src="{{ asset('dist/img/axia-logo.svg') }}" alt="Axia SMS"
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
    <script>$.widget.bridge('uibutton', $.ui.button)</script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>

    <script src="{{ asset('js/show-flash.js') }}"></script>
    <script src="{{ asset('js/global-search.js') }}"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>

    @stack('scripts')
</body>

</html>
