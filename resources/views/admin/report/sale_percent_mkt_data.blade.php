
@extends('layout.default')
@section('title') Admin | Báo Cáo tỷ lệ chốt sale theo data MKT @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo Cáo tỷ lệ chốt sale theo data MKT'),
'content' => [
__('Báo Cáo tỷ lệ chốt sale theo data MKT') => ''
],
'active' => [__('Báo Cáo tỷ lệ chốt sale theo data MKT')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{asset('js/reports/common.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/css/jquery.dataTables.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        $.get( "{{ route('admin.report.get_data_sale_percent_mkt_data') }}?from=" + $("#from").val() + "&to=" + $("#to").val(), function( data ) {
            $("#html").html(data);
            $("#ReportTable").DataTable();
        });
    </script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <form method="get">
                            <div class="row">
                                <div class="col-md-9 d-inline-flex">
                                    <span class="lbl-time">
                                        <strong>
                                            Thời gian:
                                        </strong>
                                    </span>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <input name="from" id="from" class="form-control datepicker" type="text"
                                                   value="{{request('from') != null ? request('from') : date('01/m/Y')}}">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <input name="to" id="to" class="form-control datepicker" type="text"
                                                   value="{{request('to') != null ? request('to') : date('d/m/Y')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center col-md-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary mr-3">Xem báo cáo</button>
                                    <button type="button" class="btn btn-default" onclick="printDiv('ifrmPrint','reportForm')"><i class="fas fa-print"></i> In</button>
                                </div>
                            </div>

                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="reportForm">
                      <div class="row">
                          <div class="col-lg-12 mb-5">
                              <table width="100%" border="0">
                                  <tbody>
                                  <tr>
                                      <th width="30%" style="text-align: left;">
                                          <div>{!! auth()->user()->name !!}</div>
                                          <div>Điện thoại: {{auth()->user()->phone}}</div>
                                          <div>Địa chỉ: {{auth()->user()->address}}</div>
                                          <div>
                                              <div>Chú ý: </div>
                                              <div style="font-weight: 200">%Hotline = Tổng số đơn chốt hotline /  Tổng số đơn chia hotline</div>
                                              <div style="font-weight: 200">%Khách cũ = Tổng số đơn chốt khách cũ /  Tổng số đơn chia</div>
                                          </div>
                                      </th>
                                      <th width="40%" style="text-align: center;">
                                          <h2>Báo Cáo tỷ lệ chốt sale theo data MKT</h2>
                                          <div>Ngày {{request('from') != null ? request('from') : date('01/m/Y')}}
                                              đến {{request('to') != null ? request('to') : date('d/m/Y')}}</div>
                                      </th>
                                      <th width="30%" style="text-align: right;">
                                          <div>Ngày in: {{date('d/m/Y')}}</div>
                                          <div>Tài khoản in: {{auth()->user()->account_id}}</div>
                                      </th>
                                  </tr>
                                  </tbody>
                              </table>
                          </div>
                          <div class="col-md-12 overflow-auto" id="html">
                            Đang tải vui lòng chờ trong giây lát...
                          </div>
                      </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
@stop
