@extends('layout.default')
@section('title') Admin | Báo cáo tổng hợp sale @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo tổng hợp sale'),
'content' => [
__('Báo cáo tổng hợp sale') => ''
],
'active' => [__('Báo cáo tổng hợp sale')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{ url("js/reports/common.js") }}"></script>
    <script src="{{asset('js/plugins/jquery.doubleScroll.js')}}"></script>
    <script>
        $('.table-responsive').doubleScroll({
            resetOnWindowResize:true
        })
    </script>
  <style>
    .table td, .table th {
      vertical-align: middle;
    }
    @media print{
      #responsive_tb{
        height:100%;
        width: 100%;
        overflow:visible!important;
      }
      #page, #page * {
        visibility: visible;
      }
      #page {
        position: absolute;
        top: 0;
        left: 0;

      }
      /* body {
          writing-mode: tb-rl;
      } */
      @page {
        size: landscape;
        scale:80;
        -webkit-transform: rotate(-90deg); -moz-transform:rotate(-90deg);
        filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);

      }
    }
  </style>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                      <form action="{{route('admin.report.aggregate_sale')}}" method="post" id="search">
                        @csrf
                        <div class="row">
                          <div class="col-md-9 d-flex">
                            <span class="" style="line-height: 34px;width:15%">Thời gian:</span>
                            <input name="create_date_from" id="create_date_from" class="form-control w-25 mr-3 datepicker" type="text"
                              value="{{date('d/m/Y',strtotime($conditions['create_date_from']))}}">
                            <input name="create_date_to" id="create_date_to" class="form-control w-25 mr-3 datepicker" type="text"
                              value="{{date('d/m/Y',strtotime($conditions['create_date_to']))}}">
                              <select name="user_type" id="" class="form-control w-25 mr-3">
                                <option value="{{ ACTIVE }}"
                                  {{isset($conditions['user_type']) && $conditions['user_type'] == ACTIVE ? 'selected' : ''}}>Tài khoản kích hoạt</option>
                                <option value="{{ INACTIVE }}"
                                {{isset($conditions['user_type']) && $conditions['user_type'] == INACTIVE ? 'selected' : ''}}>Tài khoản chưa kích hoạt</option>
                                <option value="">Tất cả</option>
                              </select>
                              <select name="user_groups" id="" class="form-control w-25 mr-3">
                                <option value="">Tất cả các nhóm tài khoản</option>
                                @foreach ($user_groups as $key => $item)
                                  <option value="{{$key}}"
                                  {{isset($conditions['user_groups']) && $conditions['user_groups'] == $key ? 'selected' : ''}}>{{$item}}</option>
                                @endforeach
                              </select>
                          </div>
                          <div class="text-center col-md-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary mr-3">Xem báo cáo</button>
                            <button type="button" class="btn btn-default" id="print"><i class="fas fa-print"></i> In</button>
                          </div>
                        </div>
                      </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-2">
                          <ul class="list-unstyled">
                            <li>{{$userLogin->name}}</li>
                            <li>Điện thoại: {{$userLogin->phone}}</li>
                            <li>Địa chỉ: {{$userLogin->address}}</li>
                          </ul>
                        </div>
                        <div class="col-md-8 text-center">
                          <h2>Báo cáo tổng hợp sale</h2>
                          <span>Ngày {{date('d/m/Y', strtotime($conditions['create_date_from']))}} đến {{date('d/m/Y',strtotime($conditions['create_date_to']))}}</span>
                        </div>
                        <div class="col-md-2 text-right">
                          <ul class="list-unstyled">
                            <li>Ngày in: {{date('d/m/Y')}}</li>
                            <li>Tài khoản in: {{$userLogin->account_id}}</li>
                          </ul>
                        </div>
                      </div>
                      <div class="table-responsive" id="responsive_tb">
                        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                          <thead>
                              <tr style="">
                                  <td rowspan="2" class="th-fixed">Nhân viên</td>
                                  <td rowspan="2">Số được chia</td>
                                  <td rowspan="2">Tiếp cận</td>
                                  <td colspan="4" class="text-center">TỶ LỆ VỀ SỐ</td>
                                  <td colspan="{{count($orderStatus) + 4}}" class="text-center">TÌNH TRẠNG SỐ</td>
                                  <td colspan="6" class="text-center">DOANH THU</td>
                                  <td colspan="3" class="text-center">TỶ LỆ DOANH THU</td>
                              </tr>
                              <tr>
                                  <td>Tỷ lệ tiếp cận</td>
                                  <td>CM/ Tiếp cận</td>
                                  <td>CM/ Số được chia</td>
                                  <td>Chốt mới</td>
                                  <td>Chăm sóc</td>
                                  <td>Tối ưu</td>
                                  @foreach ($orderStatus as $id => $item)
                                    <td class=" text-center">{{$item}}</td>
                                  @endforeach
                                  <td class="">TỔNG</td>
                                  <td>CHỐT MỚI</td>
                                  <td>Chăm sóc</td>
                                  <td>Đặt lại</td>
                                  <td>Đơn hủy</td>
                                  <td>HOÀN</td>
                                  <td>TỔNG</td>
                                  <td>DTBQ 1 đơn chốt mới</td>
                                  <td>DTBQ/1 SĐT</td>
                                  <td>DTTB/NGÀY CÔNG</td>
                              </tr>
                          </thead>
                          <tbody>
                            @php
                              $totalOrder = 0;
                              $totalOrderAccess= 0;
                              $totalOrderNew = 0;
                              $totalOrderCare = 0;
                              $totalOrderOptimal = 0;
                              $totalOrderStatus = [];
                              $totalAllStatus = 0;
                              foreach ($orderStatus as $index => $status){
                                $totalOrderStatus[$index] = 0;
                              }
                              $totalOrderNewPrice = 0;
                              $totalOrderCarePrice = 0;
                              $totalOrderAgianPrice = 0;
                              $totalOrderCancelPrice = 0;
                              $totalOrderReturnPrice = 0;
                              $totalRevenue = 0;
                            @endphp

                            @foreach ($data as $user)
                            @php
                                $totalOfUser = $user['count_total'];;
                                $revenueTotalOfUser = 0;
                                $totalStatus = 0;

                                $rateNewAccess = $user->count_access_order > 0 ? round($user->count_new_order / $user->count_access_order * 100, 2) : 0;
                                $rateAccess = $totalOfUser > 0 ? round($user->count_access_order / $totalOfUser * 100, 2) : 0;
                                $rateNewShare = $totalOfUser > 0 ? round($user->count_new_order / $totalOfUser * 100, 2) : 0;

                                $averageRevenueNew = $user->count_new_order > 0 ? round($user->sum_new_order / $user->count_new_order, 2) : 0;

                            @endphp
                            <tr>
                              <td class="th-fixed">{{$user->user_name}}</td>
                              <td>{{number_format($totalOfUser)}}</td>
                              <td>{{number_format($user->count_access_order)}}</td>
                              <td>{{$rateAccess.'%'}}</td>
                              <td>{{$rateNewAccess.'%'}}</td>
                              <td>{{$rateNewShare.'%'}}</td>
                              <td>{{number_format($user->count_new_order)}}</td>
                              <td>{{number_format($user->count_care_order)}}</td>
                              <td>{{number_format($user->count_optimal_order)}}</td>
                              @foreach ($orderStatus as $idStatus => $item)
                                @php
                                  $count = 'count_'.$idStatus;
                                  $sum = 'sum_'.$idStatus;
                                  $orderOfStatus = $user->$count;
                                  $totalStatus += $orderOfStatus;
                                  $totalOrderStatus[$idStatus] += $orderOfStatus;
                                  if ($idStatus != 1 || $idStatus!=8) {
                                    $revenueTotalOfUser +=  $user->$sum;
                                  }
                                @endphp
                                <td>{{number_format($orderOfStatus)}}</td>
                              @endforeach
                              <td class="">{{number_format($totalStatus)}}</td>
                              <td>{{ number_format($user->sum_new_order) }}</td>

                              <td>{{number_format($user->sum_care_order)}}</td>
                              <td>{{number_format($user->sum_again_order)}}</td>
                              <td>{{ number_format($user->sum_3) }}</td>
                              <td>{{ number_format($user->sum_6) }}</td>
                              <td>{{number_format($revenueTotalOfUser)}}</td>
                              <td>{{number_format($averageRevenueNew)}}</td>
                              <td>{{$totalOfUser > 0 ? number_format($revenueTotalOfUser / $totalOfUser) : 0 }}</td>
                              <td>{{$diffDay > 0 ? number_format($revenueTotalOfUser / $diffDay) : 0 }}</td>
                            </tr>
                            @php
                                //all
                                $totalOrder += $totalOfUser;
                                $totalOrderAccess += $user->count_access_order;
                                $totalOrderNew += $user->count_new_order;
                                $totalOrderCare += $user->count_care_order;
                                $totalOrderOptimal += $user->count_optimal_order;
                                $totalOrderNewPrice += $user->sum_new_order;
                                $totalOrderCarePrice += $user->sum_care_order;
                                $totalOrderAgianPrice += $user->sum_again_order;
                                $totalOrderCancelPrice += $user->sum_3;
                                $totalOrderReturnPrice += $user->sum_6;
                                $totalRevenue += $revenueTotalOfUser;
                            @endphp
                            @endforeach
                          </tbody>
                          <tfoot>
                              <tr>
                                  <td class="th-fixed">Tổng</td>
                                  <td class="text-bold">{{number_format($totalOrder)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderAccess)}}</td>
                                  <td class="text-center">{{$totalOrder > 0 ? round($totalOrderAccess / $totalOrder * 100, 2) : 0}} %</td>
                                  <td class="text-center">{{$totalOrderAccess > 0 ? round($totalOrderNew / $totalOrderAccess * 100, 2) : 0}} %</td>
                                  <td class="text-center">{{$totalOrder > 0 ? round($totalOrderNew / $totalOrder * 100, 2) : 0}} %</td>
                                  <td class="text-bold">{{number_format($totalOrderNew)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderCare)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderOptimal)}}</td>

                                  @foreach ($totalOrderStatus as $idStatus => $countStatus)
                                    @php
                                     $totalAllStatus += $countStatus;
                                    @endphp
                                    <td class="">
                                      {{number_format($countStatus)}}
                                    </td>
                                  @endforeach

                                  <td class="text-bold">{{number_format($totalAllStatus)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderNewPrice)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderCarePrice)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderAgianPrice)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderCancelPrice)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderReturnPrice)}}</td>
                                  <td class="text-bold">{{number_format($totalRevenue)}}</td>
                                  <td class="text-bold">{{number_format($totalOrderNew > 0 ? round($totalOrderNewPrice / $totalOrderNew, 2) : 0)}}</td>
                                  <td class="text-bold">{{number_format($totalOrder > 0 ? round($totalRevenue / $totalOrder, 2) : 0 )}}</td>
                                  <td class="text-bold">{{number_format($diffDay > 0 ? round($totalRevenue / $diffDay, 2) : 0 )}}</td>
                              </tr>
                          </tfoot>
                        </table>
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
