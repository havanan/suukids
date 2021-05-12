@extends('layout.default')
@section('title') Admin | Báo cáo đơn hàng chưa xử lý @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo đơn hàng chưa xử lý'),
'content' => [
__('Báo cáo đơn hàng chưa xử lý') => ''
],
'active' => [__('Báo cáo đơn hàng chưa xử lý')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<style>
  
</style>
   <script src="{{ url("js/reports/common.js") }}"></script>
@stop

@section('content')
<div id="section-to-print" class=""></div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <form action="{{route('admin.report.order_unprocessed')}}" method="get" id="search" class="w-100">
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
                          <h2>Báo cáo đơn hàng chưa xử lý</h2>
                          <span>Ngày {{date('d/m/Y', strtotime($conditions['create_date_from']))}} đến {{date('d/m/Y',strtotime($conditions['create_date_to']))}}</span>
                        </div>
                        <div class="col-md-2 text-right">
                          <ul class="list-unstyled">
                            <li>Ngày in: {{date('d/m/Y')}}</li>
                            <li>Tài khoản in: {{$userLogin->account_id}}</li>
                          </ul>
                        </div>
                      </div>
                      @if(count($listOrder) > 0)
                        <table class="table table-bordered ">
                          <thead class="">
                            <tr>
                              <th>STT</th>
                              <th>Mã đơn hàng</th>
                              <th>Số điện thoại</th>
                              <th>Khách hàng</th>
                              <th>Trạng thái</th>
                              <th>Kênh</th>
                              <th>NV tạo</th>
                              <th>Ngày tạo</th>
                              <th>Chia cho</th>
                              <th>Ngày chia</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($listOrder as $index => $order)
                              <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $order->code }}</td>
                                <td>{{ $order->phone }}</td>
                                <td>{{ $order->name }}</td>
                                <td>{{ $order->status_name }}</td>
                                <td>{{ $order->sources_name }}</td>
                                <td>{{ $order->user_create_name }}</td>
                                <td>{{ $order->created_at }}</td>
                                <td>{{ $order->user_shared_name }}</td>
                                <td>{{ $order->share_date }}</td>
                              </tr>
                            @endforeach
                            
                          </tbody>
                        </table>
                        <div class="text-right mt-3">
                          <span>Tổng: {{ count($listOrder) }}</span>
                        </div>
                      @else
                        <div class="text-center mt-4">Chưa có dữ liệu phù hợp.</div>
                      @endif
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
