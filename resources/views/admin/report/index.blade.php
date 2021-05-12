@extends('layout.default')
@section('title') Admin | Hệ thống báo cáo @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Hệ thống báo cáo'),
            'content' => [
                __('Hệ thống báo cáo') => route('admin.report.index')
            ],
            'active' => [__('Hệ thống báo cáo')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if(getCurrentUser()->hasPermission('view_report_sale'))
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        Báo cáo Sale
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.employee_turnover')}}">
                                    <div class="img">
                                        <i class="fas fa-money-bill-alt"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu nhân viên</h4></div>
                                        <div>Theo dõi chi tiết doanh thu nhân viên theo tháng.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.revenue_by_status')}}">
                                    <div class="img">
                                        <i class="fa fa-compass"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu theo trạng thái</h4>
                                        </div>
                                        <div>Theo dõi chi tiết doanh thu theo trạng thái các tháng.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.order_rate')}}">
                                    <div class="img">
                                        <i class="fa fa-signal"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo tỷ lệ chốt đơn hàng</h4></div>
                                        <div>Theo dõi tỷ lệ chốt đơn hàng theo ngày.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.aggregate_sale')}}">
                                    <div class="img">
                                        <i class="fa fa-table"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo tổng hợp sale</h4></div>
                                        <div>Tổng hợp tình hình số chia, tỷ lệ chốt, tổng doanh thu, tỷ lệ doanh thu của
                                            sale
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.evaluation_customer_care')}}">
                                    <div class="img">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo đánh giá CSKH</h4></div>
                                        <div>Tổng hợp tình hình CSKH, số đơn, doanh số.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.change_status')}}">
                                    <div class="img">
                                        <i class="fas fa-sync-alt"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo thay đổi trạng thái</h4></div>
                                        <div>Báo cáo thay đổi trạng thái đơn hàng.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.warehouse_sale_number')}}">
                                    <div class="img">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo kho số Sale</h4></div>
                                        <div>Theo dõi kho số Sale theo nhân viên, thời gian.</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card -->
            </div><!-- /.card -->
            @endif
            @if(getCurrentUser()->hasPermission('view_report_marketing'))
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        Báo cáo Marketing
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="#">
                                    <div class="img">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo Marketing theo khung giờ</h4>
                                        </div>
                                        <div>Theo dõi tình trạng Marketing theo khung giờ.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.marketing_revenue')}}">
                                    <div class="img">
                                        <i class="fas fa-money-bill-alt"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu Marketing</h4></div>
                                        <div>Theo dõi doanh thu của Marketing theo thời gian, nhóm tài khoản.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="#">
                                    <div class="img">
                                        <i class="fa fa-desktop"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo kho số Marketing</h4></div>
                                        <div>Theo dõi kho số Marketing theo nhân viên, thời gian.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="#">
                                    <div class="img">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Biểu đồ kho số Marketing</h4></div>
                                        <div>Biểu đồ kho số theo tháng của năm.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="#">
                                    <div class="img">
                                        <i class="fa fa-facebook-square"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo đơn hàng theo bài post</h4>
                                        </div>
                                        <div>Theo dõi đơn hàng theo bài post.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.marketing_by_source')}}">
                                    <div class="img">
                                        <i class="fa fa-globe"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo Marketing theo nguồn/kênh</h4>
                                        </div>
                                        <div>Theo dõi doanh thu Marketing theo nguồn+ Khai báo chi phí ADS hàng ngày</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.marketing_stage')}}">
                                    <div class="img">
                                        <i class="fa fa-chart-line"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo kho số MKT</h4></div>
                                        <div>Báo cáo kho số MKT</div>
                                    </div>
                                </a>
                            </div>
                        </div>


                    </div>
                </div>
                <!-- /.card -->
            </div><!-- /.card -->
            @endif
            @if(getCurrentUser()->isAdmin())
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        Báo cáo trực page, tình trạng xử lý và chia đơn
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="#">
                                    <div class="img">
                                        <i class="fa fa-female"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo trực page</h4></div>
                                        <div>Theo dõi tình trạng trực page của nhân viên.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{ route('admin.report.employee_order')}}">
                                    <div class="img">
                                        <i class="fa fa-book"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo xử lý đơn hàng của nhân
                                                viên</h4></div>
                                        <div>Theo dõi tình trạng xử lý đơn hàng của nhân viên.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{ route('admin.report.order_unprocessed')}}">
                                    <div class="img">
                                        <i class="far fa-folder"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo đơn hàng chưa xử lý</h4></div>
                                        <div>Theo dõi tình trạng đơn hàng chưa xử lý.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{ route('admin.report.warehouse_sale_number')}}">
                                    <div class="img">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo kho số Sale</h4></div>
                                        <div>Theo dõi kho số Sale theo nhân viên, thời gian.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <!-- /.card -->
            </div><!-- /.card -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        Báo cáo chung
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-sm-6">
                            <div class="report-item">
                                <a href="#">
                                    <div class="img">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Biểu đồ doanh thu nhân viên</h4></div>
                                        <div>Theo dõi doanh thu nhân viên theo tháng dưới dạng biểu đồ.</div>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.order_status')}}">
                                    <div class="img">
                                        <i class="fa fa-chart-line"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Biểu đồ trạng thái đơn hàng</h4></div>
                                        <div>Theo dõi trạng thái đơn hàng theo tháng.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.daily_turnover')}}">
                                    <div class="img">
                                        <i class="fa fa-briefcase"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu theo ngày</h4></div>
                                        <div>Theo dõi chi tiết doanh thu theo ngày.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.delivery')}}">
                                    <div class="img">
                                        <i class="fa fa-plane"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu vận chuyển</h4></div>
                                        <div>Theo dõi doanh thu vận chuyển, tỷ lệ chuyển hoàn</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.product')}}">
                                    <div class="img">
                                        <i class="fa fa-inbox"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Bảng kê sản phẩm hàng hóa</h4></div>
                                        <div>Theo dõi sản phẩm trong kho.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.product_revenue')}}">
                                    <div class="img">
                                        <i class="fas fa-money-bill-alt"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu sản phẩm, hàng
                                                hóa</h4></div>
                                        <div>Theo dõi doanh thu sản phẩm, hàng hóa.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.province')}}">
                                    <div class="img">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu theo tỉnh thành</h4></div>
                                        <div>So sánh tỷ trọng bán hàng theo tỉnh thành.</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.overview')}}">
                                    <div class="img">
                                        <i class="fa fa-chart-line"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo lương tháng</h4></div>
                                        <div>Báo cáo lương tháng team MKT</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.sale_percent_mkt_data')}}">
                                    <div class="img">
                                        <i class="fa fa-sticky-note"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo tỷ lệ chốt của Sale theo MKT (%) </h4>
                                        </div>
                                        <div>Theo dõi tỷ lệ chốt ( % ) của sale theo data Marketing</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="report-item">
                                <a href="{{route('admin.report.mkt_percent_sale_data')}}">
                                    <div class="img">
                                        <i class="fa fa-sticky-note"></i>
                                    </div>
                                    <div class="text-report">
                                        <div class="text-head"><h4 class="title">Báo cáo tỷ lệ chốt của Sale theo MKT ( doanh số )</h4>
                                        </div>
                                        <div>Theo dõi tỷ lệ chốt ( doanh số ) của marketing theo data sale</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card -->
            </div><!-- /.card -->
            @endif
        </div><!-- /.container-fluid -->
    </section>
@stop
