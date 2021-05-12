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
        <nav class="main-header navbar navbar-expand navbar-dark navbar-blue">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Doanh số tháng này: <span class="text-bold">{{ number_format($shared_dstn) }}</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Doanh số hôm nay: <span class="text-bold">{{ number_format($shared_dshn) }}</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Mục tiêu tháng này: <span class="text-bold">{{ number_format($shared_mttn) }}</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Lịch hẹn hôm nay: <span class="text-bold">{{ number_format($shared_lhhn) }}</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Xếp hạng hiện tại: <span class="text-bold">{{ $shared_bxh }}</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">So với tháng trước: <span class="text-bold">{{ $shared_percent }}%</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/admin/sell/order?status_arr%5B%5D={{ COMPLETE_ORDER_STATUS_ID }}&complete_from={{ date('d/m/Y') }}&date=complete" class="nav-link">Thành công hôm nay: <span class="text-bold">{{ $shared_dtchn }}</span></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/admin/sell/order?status_arr%5B%5D={{ REFUND_ORDER_STATUS_ID }}&refund_from={{ date('d/m/Y') }}&date=refund" class="nav-link">Đơn hoàn hôm nay: <span class="text-bold">{{ $shared_dhhn }}</span></a>
                </li>
            </ul>
            <div class="alert-header text-white" id="alert_header">
                @yield('alert-header')
            </div>


            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Messages Dropdown Menu -->
                <li class="hidden nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="javascript:;" title="Tin nhắn">
                        <i class="far fa-comments"></i>
                        <span class="badge badge-danger navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <div class="media">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Brad Diesel
                                        <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                    </h3>
                                    <p class="text-sm">Call me whenever you can...</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    </div>
                </li>
                <!-- Notifications Dropdown Menu -->
                <li class="hidden nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="javascript:;" title="Thông báo">
                        <i class="far fa-bell"></i>
                        <span class="hidden badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li>
                <!-- User Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        {!! auth()->user()->name !!}
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="{{ route('admin.user.profile') }}" class="text-center dropdown-item">
                            Trang cá nhân
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer text-danger">Đăng xuất</a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- Main Sidebar Container -->
        @include('layout.sidebar')

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
        window.auth = {name: '{{ getCurrentUser()->name }}'};
        $(document).ready(function() {
            $('title').text(`${window.auth.name} - ${window.location.pathname}`);
            // getWaitingOrders();
        });
        /*

        function getWaitingOrders() {
            $.ajax({
                url: getWaitingOrderUrl,
                type: "GET",
                success: function (data) {
                    if(data.trim()){
                        $.notify(data,{
                            'type':'warning',
                            'showProgressbar':true,
                            placement: {
                                from: "bottom",
                                align: "right"
                            }
                        });
                    }
                }
            }).fail(function(error){
            })
        }
        */
    </script>
    <script src="{{ url('js/common.js') }}"></script>
    @yield('assets')
    @section('scripts')
    @show
</body>
</html>
