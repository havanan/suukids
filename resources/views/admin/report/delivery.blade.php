
@extends('layout.default')
@section('title') Admin | BÁO CÁO DOANH THU THEO HÌNH THỨC VẬN CHUYỂN @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('BÁO CÁO DOANH THU THEO HÌNH THỨC VẬN CHUYỂN'),
'content' => [
__('BÁO CÁO DOANH THU THEO HÌNH THỨC VẬN CHUYỂN') => ''
],
'active' => [__('BÁO CÁO DOANH THU THEO HÌNH THỨC VẬN CHUYỂN')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{asset('js/reports/common.js')}}"></script>

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
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="col-sm-3 col-form-label"> Thời gian:</label>
                                        <div class="col-sm-4">
                                            <input name="from" id="from" class="form-control datepicker" type="text"
                                                   value="{{ $params['from'] }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input name="to" id="to" class="form-control datepicker" type="text"
                                                   value="{{ $params['to'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group row">
                                        <label for="staticEmail" class="col-sm-3 col-form-label"> Hình thức vận chuyển:</label>
                                        <div class="col-sm-6">
                                            <select name="delivery_id" class="form-control">
                                                <option value="">Chọn hình thức</option>
                                                @if(!empty($deliveries))
                                                    @foreach($deliveries as $key => $item )
                                                        <option value="{{$key}}" @if($params['delivery_id'] == $key) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right col-md-3">
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
                                          <h2>BÁO CÁO DOANH THU THEO HÌNH THỨC VẬN CHUYỂN</h2>
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
                          <div class="col-md-12">
                              <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                                  <tbody>
                                  <tr style="font-weight:bold;background:#DDD;">
                                      <td rowspan="2">Nhân viên</td>
                                      <td colspan="2" align="center">Thành công</td>
                                      <td colspan="2" align="center">Chuyển hoàn</td>
                                      <td colspan="2" align="center">Chuyển hàng</td>
                                  </tr>
                                  <tr>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                      <td align="center" >Số Lượng</td>
                                      <td align="center" >Doanh thu</td>
                                  </tr>
                                  <?php
                                        $count_complete = 0;
                                        $sum_complete = 0;
                                        $count_refund = 0;
                                        $sum_refund = 0;
                                        $count_delivery = 0;
                                        $sum_delivery = 0;
                                  ?>
                                  @if(isset($data) && count($data) > 0)
                                      @foreach($data as $item)
                                          <tr>
                                              <td>{!! $item->name !!} <div class="small" style="color:#999;font-style: italic;"> {{$item->account_id}} </div></td>
                                              <td align="center" >{{ number_format($item->count_complete) }}</td>
                                              <td align="center" >{{ number_format($item->sum_complete) }}</td>
                                              <td align="center" >{{ number_format($item->count_refund) }}</td>
                                              <td align="center" >{{ number_format($item->sum_refund) }}</td>
                                              <td align="center" >{{ number_format($item->count_delivery) }}</td>
                                              <td align="center" >{{ number_format($item->sum_delivery) }}</td>
                                          </tr>
                                          <?php
                                              $count_complete += $item->count_complete;
                                              $sum_complete += $item->sum_complete;
                                              $count_refund += $item->count_refund;
                                              $sum_refund += $item->sum_refund;
                                              $count_delivery += $item->count_delivery;
                                              $sum_delivery += $item->sum_delivery;
                                          ?>
                                          @endforeach
                                      @endif
                                  <tr>
                                      <td>Tổng</td>
                                      <td align="center" ><strong>{{ number_format($count_complete) }}</strong></td>
                                      <td align="center" ><strong>{{ number_format($sum_complete) }}</strong></td>
                                      <td align="center" ><strong>{{ number_format($count_refund) }}</strong></td>
                                      <td align="center" ><strong>{{ number_format($sum_refund) }}</strong></td>
                                      <td align="center" ><strong>{{ number_format($count_delivery) }}</strong></td>
                                      <td align="center" ><strong>{{ number_format($sum_delivery) }}</strong></td>
                                  </tr>
                                  </tbody>
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
