@extends('layout.default')
@section('title') Admin | Báo cáo kho số sale @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo kho số sale'),
'content' => [
__('Báo cáo kho số sale') => ''
],
'active' => [__('Báo cáo kho số sale')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{ url("js/reports/common.js") }}"></script>
    <script>
        $('[name^="order"]').change(function(){
            var $wrapper = $('.sort-wrapper');
            var key = $('[name="order_by"]').val() || 'index';
            var dir = $('[name="order"]').val() || 'asc';
            $wrapper.find('tr').sort(function(a, b) {
                if (dir == 'asc')
                return +$(a).data(key) - +$(b).data(key);
                return +$(b).data(key) - +$(a).data(key);
            })
            .appendTo($wrapper);
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
                        <form action="{{route('admin.report.warehouse_sale_number')}}" method="post" id="search" class="w-100">
                          @csrf
                          <div class="row">
                                <div class="col-md-2">
                                    <input name="create_date_from" id="create_date_from" class="form-control datepicker" type="text" value="{{date('d/m/Y',strtotime($conditions['create_date_from']))}}">
                                </div>
                                <div class="col-md-2">
                                    <input name="create_date_to" id="create_date_to" class="form-control datepicker" type="text" value="{{date('d/m/Y',strtotime($conditions['create_date_to']))}}">
                                </div>
                                <div class="col-md-2">
                                    <select name="order_type" class="form-control">
                                        <option value="">Loại đơn(Tất cả)</option>
                                        @foreach ($orderType as $id => $name)
                                        <option value="{{$id}}"
                                        {{isset($conditions['order_type']) && $conditions['order_type'] == $id ? 'selected' : ''}}>{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="order_by" class="form-control">
                                        <option value="">- Sắp xếp -</option>
                                        <option {{isset($conditions['order_by']) && $conditions['order_by'] == 'close_rate' ? 'selected' : ''}} value="close_rate">Tỷ lệ chốt</option>
                                        <option {{isset($conditions['order_by']) && $conditions['order_by'] == 'close_rate_real' ? 'selected' : ''}} value="close_rate_real">Tỷ lệ chốt thực</option>
                                        <option {{isset($conditions['order_by']) && $conditions['order_by'] == 'count_share' ? 'selected' : ''}} value="count_share">Số chia</option>
                                        <option {{isset($conditions['order_by']) && $conditions['order_by'] == 'count_called_order' ? 'selected' : ''}} value="count_called_order">Đã gọi</option>
                                        <option {{isset($conditions['order_by']) && $conditions['order_by'] == 'count_left' ? 'selected' : ''}} value="count_left">Tồn</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="order" class="form-control">
                                        <option value="">- Chiều sắp xếp -</option>
                                        <option {{isset($conditions['order']) && $conditions['order'] == 'asc' ? 'selected' : ''}} value="asc">Tăng dần</option>
                                        <option {{isset($conditions['order']) && $conditions['order'] == 'desc' ? 'selected' : ''}} value="desc">Giảm dần</option>
                                    </select>
                                </div>
                                <div class="col-md-2 text-right">
                                    <button type="submit" class="btn btn-primary mr-3">Xem báo cáo</button>
                                    <button type="button" id="print" class="btn btn-default"><i class="fas fa-print"></i> In</button>
                                </div>
                          </div>
                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="alert alert-info">Ưu tiên chia số cho người tỷ lệ chốt tốt và giảm dần cho sale còn lại</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <ul class="list-unstyled">
                                    <li>{{$userLogin->name}}</li>
                                    <li>Điện thoại: {{$userLogin->phone}}</li>
                                    <li>Địa chỉ: {{$userLogin->address}}</li>
                                </ul>
                            </div>
                            <div class="col-md-8 text-center">
                                <h2>Báo cáo kho số sale</h2>
                                <span>Ngày {{date('d/m/Y', strtotime($conditions['create_date_from']))}} đến {{date('d/m/Y',strtotime($conditions['create_date_to']))}}</span>
                            </div>
                            <div class="col-md-2 text-right">
                                <ul class="list-unstyled">
                                    <li>Ngày in: {{date('d/m/Y')}}</li>
                                    <li>Tài khoản in: {{$userLogin->account_id}}</li>
                                </ul>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <th>Tên tài khoản</th>
                                <th>Họ và tên</th>
                                <th class="text-center">Tỷ lệ chốt%</th>
                                <th class="text-center">Tỷ lệ chốt thực%</th>
                                <th class="text-center">Số chia</th>
                                <th class="text-center">Đã gọi</th>
                                <th class="text-center">Tồn</th>
                            </thead>
                            <tbody class="sort-wrapper">
                                @php
                                    $totalOrderShare = 0;
                                    $totalOrderApproached = 0;
                                    $totalCalled = 0;
                                @endphp
                                @foreach ($data as $index=>$user)
                                    @php
                                      $totalOrderShare += $user->count_share;
                                      $totalCalled += $user->count_called_order;
                                      $totalOrderApproached += $user->count_share - $user->count_called_order;

                                      $rateClose = $user->count_total > 0 ? round($user->count_close_order / $user->count_total * 100, 2) : 0;
                                      $rateCloseReal = $user->count_access_order > 0 ? round($user->count_close_order / $user->count_access_order * 100, 2) : 0;
                                    @endphp
                                    <tr data-index="{{ count($data) - $index }}" data-close_rate="{{ $rateClose }}" data-close_rate_real="{{ $rateCloseReal }}" data-count_share="{{ $user->count_share }}" data-count_called_order="{{ $user->count_called_order }}" data-count_left="{{ $user->count_share - $user->count_called_order }}">
                                        <td>{{$user->account_id}}</td>
                                        <td>{{$user->user_name}}</td>
                                        <td class="text-right">{{$rateClose.'%'}}</td>
                                        <td class="text-right">{{$rateCloseReal.'%'}}</td>
                                        <td class="text-right">{{number_format($user->count_share)}}</td>
                                        <td class="text-right">{{number_format($user->count_called_order)}}</td>
                                        <td class="text-right">{{number_format($user->count_share - $user->count_called_order)}}</td>
                                    </tr>
                                @endforeach
                                    <tr>
                                       <td><strong>Tổng</strong></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td class="text-right"><strong>{{number_format($totalOrderShare)}}</strong></td>
                                       <td class="text-right"><strong>{{number_format($totalCalled)}}</strong></td>
                                       <td class="text-right"><strong>{{number_format($totalOrderApproached)}}</strong></td>
                                    </tr>
                            </tbody>
                        </table>
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
