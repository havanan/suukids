@extends('layout.default')
@section('title') Admin | Dashboard @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
        'title' => __('Dashboard'),
        'content' => [
        __('Dashboard') => url("/")
        ],
        'active' => [__('Dashboard')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}
@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    {{--    <script src="{{url('js/source.js')}}"></script>--}}
    <script src="{{url('js/common.js')}}"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            Common.datePicker('.date-picker');
        })
    </script>
    @include('layout.flash_message')
    <script src="{{url('theme/admin-lte/plugins/highcharts/highcharts.js')}}"></script>
    <script src="{{url('theme/admin-lte/plugins/highcharts/data.js')}}"></script>
    <script src="{{url('theme/admin-lte/plugins/highcharts/exporting.js')}}"></script>
    <script src="{{url('js/dashboard.js')}}"></script>
    <script>
        var chart_title = '';
        var sale_report = JSON.parse('<?php echo json_encode($sale_report) ?>');
        var product_report = JSON.parse('<?php echo json_encode($product_report) ?>');
        var staff_report = JSON.parse('<?php echo json_encode($chart_year) ?>');
        var date_report = JSON.parse('<?php echo json_encode($chart_date) ?>')
        @if(getCurrentUser()->isAdmin ())
        makeSaleReport(sale_report);
        makeProductReport(product_report);
        makeStaffReport(staff_report);
        @endif
        makeDaeReport(date_report);
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- /.modal -->
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="get">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" class="form-control date-picker" name="from"
                                                           id="from" value="{{date('d/m/Y',strtotime($group_date[0]))}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" class="form-control date-picker" name="to"
                                                           id="to" value="{{date('d/m/Y',strtotime($group_date[1]))}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-default"><i class="fa fa-search mr-2"></i>T??m
                                                    ki???m
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            {{--S??? li???u Th???ng k???--}}
                            <div class="row">
                                <div class="tt_cmt col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> T???ng nh??n vi??n</span>
                                            <span class="info-box-number" id="TotalPreOrder">{{$user_count}}</span>
                                            <div class="more_tooltip" data-toggle="tooltip"
                                                 data-widget="chat-pane-toggle"
                                                 data-original-title="T???ng s??? t??i kho???n (Kh??ng t??nh t??i kho???n kh??ng k??ch ho???t)">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt_inbox col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                                <span class="info-box-icon"><i class="fa fa-truck"
                                                                               aria-hidden="true"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> ????n ??ang v???n chuy???n</span>
                                            <span class="info-box-number"
                                                  id="TotalOrder">{{number_format($data['transporting'])}}</span>
                                            <div class="more_tooltip" data-toggle="tooltip"
                                                 data-widget="chat-pane-toggle"
                                                 data-original-title="T???ng s??? ????n ??ang v???n chuy???n trong kho???ng th???i gian xem">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="don_xac_nhan col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                                <span class="info-box-icon"><i class="fa fa-copy"
                                                                               aria-hidden="true"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> ????n X??c Nh???n / ???????c Chia </span>
                                            <span class="info-box-number" id="TotalOrderRevenue">{{number_format($data['confirmed'])}}/{{number_format($data['divided'])}}</span>
                                            <div class="more_tooltip" data-toggle="tooltip"
                                                 data-widget="chat-pane-toggle"
                                                 data-original-title="T???ng c??c ????n ???????c x??c nh???n / T???ng s??? ???? ??u???c chia">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="tt_ds col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                                <span class="info-box-icon"><i class="fa fa-money-bill"
                                                                               aria-hidden="true"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> Doanh S??? / DS tr??? ho??n </span>
                                            <span class="info-box-number" id="ConvertRate">{{number_format($data['sales'], 2)}}<span
                                                    class="small text-success">tr.??</span> / {{number_format($data['deducted'], 2)}}<span
                                                    class="small text-success">tr.??</span></span>
                                            <div class="more_tooltip" data-toggle="tooltip"
                                                 data-widget="chat-pane-toggle"
                                                 data-original-title="Doanh thu tr??n c??c ????n x??c nh???n v?? doanh s??? tr??? ho??n">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="ty_le_chot col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                                <span class="info-box-icon"><i class="fa fa-check-circle"
                                                                               aria-hidden="true"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> T??? L??? Ch???t </span>
                                            <span class="info-box-number"
                                                  id="TotalWaittingGet">{{$data['percent']}}%</span>
                                            <div class="more_tooltip" data-toggle="tooltip" title=""
                                                 data-widget="chat-pane-toggle"
                                                 data-original-title="T??? L??? Ch???t">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="don_chua_xu_ly col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                                <span class="info-box-icon"><i class="fa fa-list-ul"
                                                                               aria-hidden="true"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> Ch??a X??? L?? </span>
                                            <span class="info-box-number"
                                                  id="TotalGet">{{number_format($data['no_process'])}}</span>
                                            <div class="more_tooltip" data-toggle="tooltip" title=""
                                                 data-widget="chat-pane-toggle"
                                                 data-original-title="T???ng ????n h??ng ??ang ??? tr???ng th??i ch??a x??c nh???n">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- S??? li???u Th???ng k???--}}

                            {{-- Bi???u ?????--}}
                            <div class="row">
                                {{-- Bi???u ?????--}}
                                @if(getCurrentUser()->isAdmin ())
                                    <div class="col-md-6">
                                        <div id="saleReportContainer"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="productReportContainer" ></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="staffReportContainer"></div>
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <div id="doanhThuReportContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
