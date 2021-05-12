@extends('layout.default')
@section('title') Admin | Báo cáo xử lý đơn hàng của nhân viên @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo xử lý đơn hàng của nhân viên'),
'content' => [
__('Báo cáo xử lý đơn hàng của nhân viên') => ''
],
'active' => [__('Báo cáo xử lý đơn hàng của nhân viên')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
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
                        <form action="{{route('admin.report.employee_order')}}" method="get" id="search" class="w-100">
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
                          <h2>Báo cáo xử lý đơn hàng của nhân viên</h2>
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
                        <table class="table table-bordered ">
                          <thead class="">
                            <tr>
                              <th>Tên tài khoản</th>
                              <th>Nhân viên</th>
                              @foreach ($orderStatus as $item)
                                <th>{{ $item }}</th>
                              @endforeach
                              <th>Tổng</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($userList as $user)
                              <tr>
                                <td>{{ $user->account_id }}</td>
                                <td>{{ $user->name }}</td>
                                @php
                                  $total= 0;
                                  $userId = $user->id;
                                  foreach ($orderStatus as $id => $name){
                                      $count = $order->where(['shop_id' => $userLogin->shop_id, 'status_id' => $id])
                                        ->where(function ($query) use ($userId) {
                                            $query
                                                ->where('user_created', '=', $userId)
                                                ->orWhere('upsale_from_user_id', '=', $userId)
                                                ->orWhere('assigned_user_id', '=', $userId)
                                                ->orWhere('marketing_id', '=', $userId);
                                        })
                                        ->whereBetween('created_at', [$conditions['create_date_from'], $conditions['create_date_to']])->count();
                                    echo '<td>'.number_format($count).'</td>';
                                    $total += $count;
                                  }
                                @endphp
                                <td>{{number_format($total)}}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                      {{ $userList->appends($_GET)->links() }}
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
