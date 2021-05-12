<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ url('assets/release/css/layout.css') }}?{{ filemtime('assets/release/css/layout.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        var token = "{{ csrf_token() }}";
    </script>
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    @yield('header')
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <!-- SEARCH FORM -->
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <img src="@if(auth()->user()['avatar']){{url(auth()->user()['avatar'])}}@else {{url('theme/admin-lte/dist/img/no_avatar.webp')}} @endif"
                            class="img-circle elevation-2" width="32px" height="32px"
                            alt="User Image">{!! auth()->user()['name'] !!}
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                        <div class="text-center">
                            <a href="{{ route('superadmin.logout') }}" class="dropdown-item text-red">
                                Đăng xuất
                            </a>
                        </div>
                        <div class="dropdown-divider"></div>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layout.superadmin.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    {{-- Breadcrumb --}}
                    @yield('breadcrumb')
                    {{-- End breadcrumb --}}
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2020 kpwzto.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.0.2
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->
    <script src="{{ url('assets/release/js/layout.js') }}?{{ filemtime('assets/release/js/layout.js') }}"></script>
    <script>
        var searchOrderByPhoneUrl = "{{route('admin.sell.order.commonSearch')}}"
        var indexOrderUrl = "{{route('admin.sell.order.index')}}"
        let getWaitingOrderUrl = "{{route('admin.sell.order.waiting_orders')}}"
    </script>
    <script src="{{ url('js/common.js') }}"></script>
    @yield('assets')
</body>
@include('layout.firebase')
@include('layout.tawkto')
</html>
