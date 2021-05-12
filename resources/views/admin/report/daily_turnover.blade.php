@extends('layout.default')
@section('title') Admin | Báo Cáo Đơn Hàng Theo Ngày @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo Cáo Đơn Hàng Theo Ngày'),
'content' => [
__('Báo Cáo Đơn Hàng Theo Ngày') => ''
],
'active' => [__('Báo Cáo Đơn Hàng Theo Ngày')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{ url("js/reports/common.js") }}"></script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                      
                      <form action="{{route('admin.report.daily_turnover')}}" method="get" class="form-inline">
                        <div class="row w-100">
                            <div class="col-md-6">
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">Ngày</span>
                                </div>
                                <input type="text" name="create_date_from" class="form-control datepicker" value="{{date('d/m/Y',strtotime($conditions['create_date_from']))}}">
                                <input type="text" name="create_date_to" class="form-control datepicker" value="{{date('d/m/Y',strtotime($conditions['create_date_to']))}}">
                              </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                                <div class="input-group ">
                                    <input name="view_report" class="btn btn-primary" value="Xem báo cáo" type="submit">
                                    <button type="button" class="btn btn-default ml-3" id="print"><i class="fa fa-print"></i> In báo cáo</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="form_block_id" value="73109">
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
                          <h2>Báo Cáo Đơn Hàng Theo Ngày</h2>
                          <span>Ngày {{date('d/m/Y', strtotime($conditions['create_date_from']))}} đến {{date('d/m/Y',strtotime($conditions['create_date_to']))}}</span>
                        </div>
                        <div class="col-md-2 text-right">
                          <ul class="list-unstyled">
                            <li>Ngày in: {{date('d/m/Y')}}</li>
                            <li>Tài khoản in: {{$userLogin->account_id}}</li>
                          </ul>
                        </div>
                      </div>
                      <table width="100%" class="table table-bordered" bordercolor="#000">
                        <thead>
                          <th>Khách hàng</th>
                          <th>Điện thoại</th>
                          <th>Sản phẩm</th>
                          <th>Chi phí chốt</th>
                          <th>Xác nhận</th>
                          <th>Hủy</th>
                          <th>Ghi chú</th>
                        </thead>  
                        <tbody>
                          @foreach ($listOrder as $item)
                            <tr>
                              <td>{{$item->name}}</td>
                              <td>{{$item->phone}}</td>
                              <td>{{$item->getProductsNameAttribute()}}</td>
                              <td>{{number_format($item->total_price)}}</td>
                              <td>{{$item->status_id == CLOSE_ORDER_STATUS_ID ? 'Đã XN' : '' }}</td>
                              <td>{{$item->status_id == CANCEL_ORDER_STATUS_ID ? 'Đã HỦY' : ''}}</td>
                              <td>{{$item->note1}} {{$item->note2}}</td>
                            </tr>
                          @endforeach
                            
                        </tbody>
                    </table>
                    {{ $listOrder->appends($_GET)->links() }}
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
