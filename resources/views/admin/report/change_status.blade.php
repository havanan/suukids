@extends('layout.default')
@section('title') Admin | Báo cáo thay đổi trạng thái @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo thay đổi trạng thái'),
'content' => [
__('Báo cáo thay đổi trạng thái') => ''
],
'active' => [__('Báo cáo thay đổi trạng thái')]
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
                            <input name="create_date_from" id="create_date_from" class="form-control w-25 mr-3" type="text" 
                              placeholder="Mã đơn hàng">
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
                          <h2>Báo cáo thay đổi trạng thái</h2>
                          <span>Ngày {{date('01/m/Y')}} đến {{date('d/m/Y')}}</span>
                        </div>
                        <div class="col-md-2 text-right">
                          <ul class="list-unstyled">
                            <li>Ngày in: {{date('d/m/Y')}}</li>
                            <li>Tài khoản in: Hunter007</li>
                          </ul>
                        </div>
                      </div>
                      <table width="100%" class="table table-bordered" bordercolor="#CCC" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                           <tr>
                              <th>STT</th>
                              <th>Mã đơn hàng</th>
                              <th>Khách hàng</th>
                              <th>Điện thoại chính</th>
                              <th>Điện thoại phụ</th>
                              <th>Ghi chú</th>
                              <th>Trạng thái</th>
                              <th>NV tạo</th>
                              <th>Tên sản phẩm</th>
                              <th>Tổng tiền</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td>1</td>
                              <td>70647599</td>
                              <td>Tuấn</td>
                              <td>23423423</td>
                              <td></td>
                              <td class="small">
                                 <div></div>
                                 <div></div>
                              </td>
                              <td>
                                 <div class="small text-danger">Đã thu tiền -&gt; Thành công<br>Bởi <strong>hunter117</strong> lúc 21:40' 09/04/2020</div>
                              </td>
                              <td class="small">hunter117<br>21:38' 09/04/2020</td>
                              <td>
                                 1 Sony 
                              </td>
                              <td class="text-right">10,000</td>
                           </tr>
                           <tr>
                              <td>2</td>
                              <td>70643346</td>
                              <td>đá</td>
                              <td>23423423</td>
                              <td></td>
                              <td class="small">
                                 <div></div>
                                 <div></div>
                              </td>
                              <td>
                                 <div class="small">Chưa xác nhận<br>Bởi <strong>hunter117</strong> lúc 20:49' 09/04/2020</div>
                              </td>
                              <td class="small">hunter117<br>20:49' 09/04/2020</td>
                              <td></td>
                              <td class="text-right">0</td>
                           </tr>
                        </tbody>
                     </table>
                     <div class="row float-right mt-3">
                       <span>Tổng: 10,000/2 đơn</span>
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
