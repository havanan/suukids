@extends('layout.default')
@section('title') Admin | Báo Cáo Tỷ lệ chốt @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo Cáo Tỷ lệ chốt'),
'content' => [
__('Báo Cáo Tỷ lệ chốt') => ''
],
'active' => [__('Báo Cáo Tỷ lệ chốt')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<script>
   Common.datePicker(".datepicker");
</script>
  <style>
    #section-to-print, #section-to-print * {
        visibility: hidden;
        height:0px;
    }
    @media print {
      body * {
        visibility: hidden;
      }
      #section-to-print, #section-to-print * {
        visibility: visible;
        height:auto;
      }
      #section-to-print {
        position: absolute;
        left: 0;
        top: 0;
      }
      .card-header{
        display: none!important;
      }
      .print-title-section{
        display: block!important;
      }
    }
  </style>

  <script>
    function PrintDiv() {
      $('#section-to-print').html('');
      let content = $('#print_content').clone();
      content.appendTo("#section-to-print")
      $('#section-to-print').find('.content-table').removeClass('col-sm-6');
      $('#section-to-print').find('.content-table').addClass('col-sm-12');
      window.print();
    }
  </script>
@stop

@section('content')
<div id="section-to-print" class=""></div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <form action="{{route('admin.report.order_rate')}}" method="post" id="search" class="w-100">
                            @csrf
                          <div class="row">
                            <div class="col-md-6 d-inline-flex">
                              <span class="mr-3" style="line-height: 34px;">Thời gian:</span>
                              <input name="create_date_from" id="create_date_from" class="form-control  datepicker w-25 mr-3" type="text"
                                      value="{{date('d/m/Y',strtotime($conditions['create_date_from']))}}">
                              <input name="create_date_to" id="create_date_to" class="form-control  datepicker w-25 mr-3" type="text"
                                      value="{{date('d/m/Y',strtotime($conditions['create_date_to']))}}">
                            </div>
                            <div class="text-center col-md-6 d-flex justify-content-end">
                              <button type="submit" class="btn btn-primary mr-3">Xem báo cáo</button>
                              <button type="button" class="btn btn-default" onclick="PrintDiv()" id="print"><i class="fas fa-print"></i> In</button>
                            </div>
                          </div>
                        </form>
                        <div class="row">
                          <ul class="list-unstyled mt-3 ml-2">
                            <li>*Tỷ lệ chốt = ( số chốt / số chia)</li>
                            <li>*Tỷ lệ chốt thật = ( số chốt / số chia tiếp cận được)</li>
                          </ul>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="print_content">
                      <div class="row">
                        <div class="col-sm-6 content-table">
                          <h1 class="print-title-section d-none pl-4">Hôm nay</h1>
                          <div class="card">
                            <div class="card-header bg-danger">
                              Hôm nay
                            </div>
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Nhân viên</th>
                                    <th>Tổng đơn được chia</th>
                                    <th>Xác nhận</th>
                                    <th>Hủy</th>
                                    <th>Tỷ lệ chốt</th>
                                    <th>Tỷ lệ chốt thật</th>
                                    <th>%Hủy</th>
                                    <th>Doanh thu</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($dataToday as $user)
                                    @php
                                      $rateClose = $user->count_total > 0 ? round($user->count_close_order / $user->count_total * 100, 2) : 0;
                                      $rateCloseReal = $user->count_access_order > 0 ? round($user->count_close_order / $user->count_access_order * 100, 2) : 0;
                                      $rateCancel = $user->count_total > 0 ? round($user->count_cancel_order / $user->count_total * 100, 2) : 0;

                                    @endphp
                                    <tr>
                                      <td>{{$user->user_name}}</td>
                                      <td>{{number_format($user->count_total)}}</td>
                                      <td>{{number_format($user->count_close_order)}}</td>
                                      <td>{{number_format($user->count_cancel_order)}}</td>
                                      <td>{{$rateClose.'%'}}</td>
                                      <td>{{$rateCloseReal.'%'}}</td>
                                      <td>{{$rateCancel.'%'}}</td>
                                      <td>{{number_format($user->sum_total_order)}}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6 content-table">
                          <h1 class="print-title-section d-none pl-4">Hôm qua</h1>
                          <div class="card">
                            <div class="card-header bg-success">
                              Hôm qua
                            </div>
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Nhân viên</th>
                                    <th>Tổng đơn được chia</th>
                                    <th>Xác nhận</th>
                                    <th>Hủy</th>
                                    <th>Tỷ lệ chốt</th>
                                    <th>Tỷ lệ chốt thật</th>
                                    <th>%Hủy</th>
                                    <th>Doanh thu</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($dataYesterday as $user)
                                    @php
                                      $rateClose = $user->count_total > 0 ? round($user->count_close_order / $user->count_total * 100, 2) : 0;
                                      $rateCloseReal = $user->count_access_order > 0 ? round($user->count_close_order / $user->count_access_order * 100, 2) : 0;
                                      $rateCancel = $user->count_total > 0 ? round($user->count_cancel_order / $user->count_total * 100, 2) : 0;

                                    @endphp
                                    <tr>
                                      <td>{{$user->user_name}}</td>
                                      <td>{{number_format($user->count_total)}}</td>
                                      <td>{{number_format($user->count_close_order)}}</td>
                                      <td>{{number_format($user->count_cancel_order)}}</td>
                                      <td>{{$rateClose.'%'}}</td>
                                      <td>{{$rateCloseReal.'%'}}</td>
                                      <td>{{$rateCancel.'%'}}</td>
                                      <td>{{number_format($user->sum_total_order)}}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6 content-table">
                          <h1 class="print-title-section d-none pl-4">Tháng này ({{date("d",strtotime($conditions['create_date_from']))}}-{{date("d/m",strtotime($conditions['create_date_to']))}})</h1>
                          <div class="card">
                            <div class="card-header bg-danger">
                              Tháng này ({{date("d",strtotime($conditions['create_date_from']))}}-{{date("d/m",strtotime($conditions['create_date_to']))}})
                            </div>
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Nhân viên</th>
                                    <th>Tổng đơn được chia</th>
                                    <th>Xác nhận</th>
                                    <th>Hủy</th>
                                    <th>Tỷ lệ chốt</th>
                                    <th>Tỷ lệ chốt thật</th>
                                    <th>%Hủy</th>
                                    <th>Doanh thu</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($dataMonth as $user)
                                    @php
                                      $rateClose = $user->count_total > 0 ? round($user->count_close_order / $user->count_total * 100, 2) : 0;
                                      $rateCloseReal = $user->count_access_order > 0 ? round($user->count_close_order / $user->count_access_order * 100, 2) : 0;
                                      $rateCancel = $user->count_total > 0 ? round($user->count_cancel_order / $user->count_total * 100, 2) : 0;

                                    @endphp
                                    <tr>
                                      <td>{{$user->user_name}}</td>
                                      <td>{{number_format($user->count_total)}}</td>
                                      <td>{{number_format($user->count_close_order)}}</td>
                                      <td>{{number_format($user->count_cancel_order)}}</td>
                                      <td>{{$rateClose.'%'}}</td>
                                      <td>{{$rateCloseReal.'%'}}</td>
                                      <td>{{$rateCancel.'%'}}</td>
                                      <td>{{number_format($user->sum_total_order)}}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6 content-table">
                          <h1 class="print-title-section d-none pl-4">Cùng kỳ tháng trước ({{date("d",strtotime($conditions['create_date_from_pre']))}}-{{date("d/m",strtotime($conditions['create_date_to_pre']))}})</h1>
                          <div class="card">
                            <div class="card-header bg-success">
                              Cùng kỳ tháng trước ({{date("d",strtotime($conditions['create_date_from_pre']))}}-{{date("d/m",strtotime($conditions['create_date_to_pre']))}})
                            </div>
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Nhân viên</th>
                                    <th>Tổng đơn được chia</th>
                                    <th>Xác nhận</th>
                                    <th>Hủy</th>
                                    <th>Tỷ lệ chốt</th>
                                    <th>Tỷ lệ chốt thật</th>
                                    <th>%Hủy</th>
                                    <th>Doanh thu</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($dataMonthPre as $user)
                                    @php
                                      $rateClose = $user->count_total > 0 ? round($user->count_close_order / $user->count_total * 100, 2) : 0;
                                      $rateCloseReal = $user->count_access_order > 0 ? round($user->count_close_order / $user->count_access_order * 100, 2) : 0;
                                      $rateCancel = $user->count_total > 0 ? round($user->count_cancel_order / $user->count_total * 100, 2) : 0;

                                    @endphp
                                    <tr>
                                      <td>{{$user->user_name}}</td>
                                      <td>{{number_format($user->count_total)}}</td>
                                      <td>{{number_format($user->count_close_order)}}</td>
                                      <td>{{number_format($user->count_cancel_order)}}</td>
                                      <td>{{$rateClose.'%'}}</td>
                                      <td>{{$rateCloseReal.'%'}}</td>
                                      <td>{{$rateCancel.'%'}}</td>
                                      <td>{{number_format($user->sum_total_order)}}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
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
@stop
