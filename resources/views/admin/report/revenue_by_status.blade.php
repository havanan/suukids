@extends('layout.default')
@section('title') Admin | Báo cáo doanh thu theo trạng thái @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo doanh thu theo trạng thái'),
'content' => [
__('Báo cáo doanh thu theo trạng thái') => ''
],
'active' => [__('Báo cáo doanh thu theo trạng thái')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
  <script>
    Common.datePicker(".datepicker");
  </script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                          <div class="col-md-6 d-inline-flex">
                            <span class="mr-3" style="line-height: 34px;">Thời gian:</span>
                            <input name="create_date_from" id="create_date_from" class="form-control w-25 mr-3 datepicker" type="text" 
                              value="{{date('01/m/Y')}}">
                            <input name="create_date_to" id="create_date_to" class="form-control w-25 datepicker" type="text" 
                              value="{{date('d/m/Y')}}">
                          </div>
                          <div class="text-center col-md-6 d-flex justify-content-end">
                            <a href="#" class="btn btn-primary mr-3">Xem báo cáo</a>
                            <button class="btn btn-default"><i class="fas fa-print"></i> In</button>
                          </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-2">
                          <ul class="list-unstyled">
                            <li>Hunter007</li>
                            <li>Điện thoại: 09898900002</li>
                            <li>Địa chỉ:HN</li>
                          </ul>
                        </div>
                        <div class="col-md-8 text-center">
                          <h2>Báo cáo doanh thu theo trạng thái</h2>
                          <span>Ngày {{date('01/m/Y')}} đến {{date('d/m/Y')}}</span>
                        </div>
                        <div class="col-md-2 text-right">
                          <ul class="list-unstyled">
                            <li>Ngày in: {{date('d/m/Y')}}</li>
                            <li>Tài khoản in: Hunter007</li>
                          </ul>
                        </div>
                      </div>
                      <table class="table table-bordered ">
                        <thead class="">
                          <tr>
                            <th>Nhân viên</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Tổng</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Tổng</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
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
