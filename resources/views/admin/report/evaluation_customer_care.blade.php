@extends('layout.default')
@section('title') Admin | Báo cáo đánh giá CSKH @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo đánh giá CSKH'),
'content' => [
__('Báo cáo đánh giá CSKH') => ''
],
'active' => [__('Báo cáo đánh giá CSKH')]
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
                          <div class="col-md-6 d-flex justify-content-end">
                            <a href="#" class="btn btn-primary mr-3">Xem báo cáo</a>
                            <button class="btn btn-default"><i class="fas fa-print"></i> In</button>
                          </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                        <tbody>
                           <tr>
                              <th rowspan="2" width="50" style=" vertical-align: middle;">STT</th>
                              <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên sản phẩm</th>
                              <th colspan="3" style="text-align: center">Đơn chăm sóc</th>
                              <th colspan="3" style="text-align: center">Doanh số</th>
                           </tr>
                           <tr>
                              <td style="text-align: center">Số đơn chăm sóc</td>
                              <td style="text-align: center">Đánh giá của NVCSKH</td>
                              <td style="text-align: center">Đánh giá của KH</td>
                              <td style="text-align: center">Doanh số</td>
                              <td style="text-align: center">Doanh số CSKH</td>
                              <td style="text-align: center">% Doanh số CSKH/Doanh thu</td>
                           </tr>
                           <tr>
                              <td colspan="8" style="text-align: center">Không có dữ liệu!</td>
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
