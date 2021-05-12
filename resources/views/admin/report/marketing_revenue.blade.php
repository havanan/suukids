<?php
    $count_close_order = 0;
    $sum_close_order = 0;
    $count_cancel_order = 0;
    $sum_cancel_order = 0;
    $count_default_order = 0;
    $sum_default_order = 0;
    $count_delivery_order = 0;
    $sum_delivery_order = 0;
    $count_return_order = 0;
    $sum_return_order = 0;
    $count_success_order = 0;
    $sum_success_order = 0;
    $count_collected_money = 0;
    $sum_collected_money = 0;
    $count_total_order = 0;
    $sum_total_order = 0;
?>
@extends('layout.default')
@section('title') Admin | Báo Cáo Doanh Thu Marketing @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo Cáo Doanh Thu Marketing'),
'content' => [
__('Báo Cáo Doanh Thu Marketing') => ''
],
'active' => [__('Báo Cáo Doanh Thu Marketing')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{asset('js/reports/common.js')}}"></script>
    <script src="{{asset('js/plugins/jquery.doubleScroll.js')}}"></script>
    <script>
        $('.table-responsive').doubleScroll({
            resetOnWindowResize:true
        })
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
                                    <div class="row">
                                        <div class="col">
                                            <input name="from" id="from" class="form-control datepicker" type="text"
                                                   value="{{request('from') != null ? request('from') : date('01/m/Y')}}">
                                        </div>
                                        <div class="col">
                                            <input name="to" id="to" class="form-control datepicker" type="text"
                                                   value="{{request('to') != null ? request('to') : date('d/m/Y')}}">
                                        </div>
                                        <div class="col">
                                            <select name="order_type" id="order_type" class="form-control">
                                                <option value="">Tất cả đơn</option>
                                                @foreach($order_types as $key => $type)
                                                    <option value="{{$type->id}}" @if(request('order_type') == $type->id) selected @endif>{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="status" class="form-control">
                                                @if(!empty($status_arr))
                                                    @foreach($status_arr as $key => $item)
                                                        <option value="{{$key}}" @if($status == $key) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                 @endif
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="type_date" class="form-control">
                                                <option value="close_date" {{ request('type_date') === 'close_date' ? 'selected' : '' }}>Tính theo ngày chốt đơn</option>
                                                <option value="created_at" {{ request('type_date') === 'created_at' ? 'selected' : '' }}>Tính theo ngày tạo</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="user_group_id" class="form-control">
                                                <option value="">Tất cả nhóm tài khoản</option>
                                                @foreach($group_types as $key => $type)
                                                    <option value="{{$type->id}}" @if(request('user_group_id') == $type->id) selected @endif>{{$type->name}}</option>
                                                @endforeach
                                            </select>
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
                                      </th>
                                      <th width="40%" style="text-align: center;">
                                          <h2>BÁO CÁO DOANH THU MARKETING</h2>
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
                          <div class="col-md-12 table-responsive">
                              <table id="ReportTable"  bordercolor="#999" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse;" class="table">
                                <thead>
                                  <tr style="font-weight:bold;background:#DDD;">
                                      <td rowspan="2">Nhân viên</td>
                                      <td colspan="2" align="center">Xác Nhận - Chốt đơn</td>
                                      <td colspan="2" align="center">Hủy</td>
                                      <td colspan="2" align="center">Kế toán mặc định</td>
                                      <td colspan="2" align="center">Chuyển hàng</td>
                                      <td colspan="2" align="center">Chuyển hoàn</td>
                                      <td colspan="2" align="center">Thành công</td>
                                      <td colspan="2" align="center">Đã thu tiền</td>
                                      <td colspan="2" align="center">Tổng</td>
                                  </tr>
                                  <tr>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                  </tr>
                                </thead>
                                  <tbody id="data_tables">

                                  @if(count($data) > 0)
                                      @foreach($data as $item)
                                      @if($item->isMarketing())
                                      <tr data-value="{{ $item->sum_total }}">
                                          <td>{!! $item->user_name !!} <div class="small" style="color:#999;font-style: italic;"> {{$item->account_id}} </div></td>
                                          <td align="center">{{number_format($item->count_5)}}</td>
                                          <td align="center">{{number_format($item->sum_5)}}</td>
                                          <td align="center">{{number_format($item->count_3)}}</td>
                                          <td align="center">{{number_format($item->sum_3)}}</td>
                                          <td align="center">{{number_format($item->count_9)}}</td>
                                          <td align="center">{{number_format($item->sum_9)}}</td>
                                          <td align="center">{{number_format($item->count_4)}}</td>
                                          <td align="center">{{number_format($item->sum_4)}}</td>
                                          <td align="center">{{number_format($item->count_6)}}</td>
                                          <td align="center">{{number_format($item->sum_6)}}</td>
                                          <td align="center">{{number_format($item->count_7)}}</td>
                                          <td align="center">{{number_format($item->sum_7)}}</td>
                                          <td align="center">{{number_format($item->count_10)}}</td>
                                          <td align="center">{{number_format($item->sum_10)}}</td>
                                          <td align="center">{{number_format($item->count_total)}}</td>
                                          <td align="center">{{number_format($item->sum_total)}}</td>
                                      </tr>
                                      <?php
                                          $count_close_order += $item->count_5;
                                          $sum_close_order += $item->sum_5;
                                          $count_cancel_order += $item->count_3;
                                          $sum_cancel_order += $item->sum_3;
                                          $count_default_order += $item->count_9;
                                          $sum_default_order += $item->sum_9;
                                          $count_delivery_order += $item->count_4;
                                          $sum_delivery_order += $item->sum_4;
                                          $count_return_order += $item->count_6;
                                          $sum_return_order += $item->sum_6;
                                          $count_success_order += $item->count_7;
                                          $sum_success_order += $item->sum_7;
                                          $count_collected_money += $item->count_10;
                                          $sum_collected_money += $item->sum_10;
                                          $count_total_order += $item->count_total;
                                          $sum_total_order += $item->sum_total;
                                      ?>
                                      @endif
                                      @endforeach
                                  @endif
                                  </tbody>
                                  <tfoot>
                                  <tr>
                                      <td>Tổng</td>
                                      <td align="center" ><strong>{{number_format($count_close_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_close_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_cancel_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_cancel_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_default_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_default_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_delivery_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_delivery_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_return_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_return_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_success_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_success_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_collected_money)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_collected_money)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($count_total_order)}}</strong></td>
                                      <td align="center" ><strong>{{number_format($sum_total_order)}}</strong></td>
                                  </tr>
                                  </tfoot>
                              </table>
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
@section('scripts')
<script>
var $wrapper = $('#data_tables');
$wrapper.find('tr').sort(function(a, b) {
    return +b.getAttribute('data-value') - +a.getAttribute('data-value');
})
.appendTo($wrapper);
</script>
@endsection
