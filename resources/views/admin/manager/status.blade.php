@extends('layout.default')
@section('title') Admin | Status @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
  'title' => __('Quản lý trạng thái'),
  'content' => [
  __('Quản lý trạng thái đơn hàng') => url("status.index")
],
'active' => [__('Quản lý trạng thái đơn hàng')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
  
  <script>
      var urlStore = '{{route('admin.status.index')}}'

      @if($message = Session::get('error'))
          toastr.error('{{$message}}')
      @endif
      @if($message = Session::get('success'))
          toastr.success('{{$message}}')
      @endif
      
  </script>
  <script src="{{ url("js/order_status.js") }}"></script>
  <style>
    #status-body td {
      position: relative;
      text-align: center!important; /* center checkbox horizontally */
      vertical-align: middle!important; /* center checkbox vertically */
    }
    #status-body td input {
      position: absolute;
      display: block;
      top:0;
      left:0;
      margin: 0;
      height: 100% !important;
      width: 100%;
      border-radius: 0 !important;
      border: none;
      padding: 10px;
      box-sizing: border-box;
    }
  </style>
@stop
@section('content')
 <!-- Main content -->
 <section class="content">
  <div class="container-fluid">
    <form method="post" action="" id="base-information">
      
      <div class="row">
        <div class="card">
            <div class="card-header border-bottom-0">
              <h3 class="card-title"> Quản lý trạng thái đơn hàng</h3>
              <button type="button"   class="btn btn-primary btn-custom-color float-right btn-save"><i class="far fa-save"></i> Lưu</button>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-md-7">
                  <div class="bs-example" data-example-id="hoverable-table" id="oldStatus" style="background:#fff;">
                    <table class="table table table-bordered" id="table-status">
                      <thead>
                          <tr>
                            <th width="40%">Tên trạng thái</th>
                            <th width="15%">Ko tiếp cận</th>
                            <th width="10%" title="Không doanh thu khi đã xác nhận">Ko tính
                                <br> doanh thu
                            </th>
                            <th width="10%">Vị trí</th>
                            <th width="40%">Màu tùy chỉnh</th>
                            <th width="5%">Level</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($listStatus as $item)
                            <tr data-id = '{{$item->id}}'>
                              <td title="">
                                {{$item->name}}
                                <input type="hidden" value="{{$item->name}}" name="edit[{{$item->id}}][name]">
                              </td>
                              <td align="center">
                                {{ $item->no_reach_flag == ACTIVE ? 'x' : '' }}
                                <input type="hidden" value="{{$item->no_reach_flag}}" name="edit[{{$item->id}}][no_reach_flag]">
                              </td>
                              <td align="center">
                                {{ $item->no_revenue_flag == ACTIVE ? 'x' : '' }}
                                <input type="hidden" value="{{$item->no_revenue_flag}}" name="edit[{{$item->id}}][no_revenue_flag]">
                              </td>
                              <td>
                              <input name="edit[{{$item->id}}][position]" type="text"  value="{{ $item->position }}" class="form-control" style="padding: 2px; text-align: center">
                              </td>
                              <td>
                                <div id="cp2" class="color-picker-ip input-group colorpicker-component colorpicker-element" title="Using input value" data-colorpicker-id="1">
                                    <input name="edit[{{$item->id}}][color]" type="text"  value="{{$item->color}}" class="form-control colorpicker-component multi-edit-text-input" >
                                    <div class="input-group-append">
                                      <span class="input-group-text color-picker" style="background-color: {{$item->color}}; "><i class="fas fa-angle-right " ></i></span>
                                    </div>
                                </div>
                              </td>
                              <td>
                                <input name="edit[{{$item->id}}][level]" type="text " max="5 " maxlength="5 " value="{{$item->level}}" 
                                      class="form-control " style="padding: 2px; text-align: center "
                                      {{ $item->is_system == ACTIVE ? 'readonly' : '' }}>
                              </td>
                            <input type="hidden" class="oldStatus" name="oldStatus[]" id="status_{{$item->id}}"
                                value="{{ $item->id.':'.$item->position.':'.$item->color.':'.$item->level.':'.$item->no_reach_flag.':'.$item->no_revenue_flag }}">
                          </tr>
                          @endforeach
                          
                      </tbody>
                    </table>
                </div>
                </div>
                <div class="col-md-5">
                  <div class="row">
                    <table class="table">
                      <tbody>
                        <tr valign="top">
                          <td>
                            <div class="alert alert-default" id="statusEdit">
                              <h3 class="card-title">Chỉnh sửa trạng thái của shop</h3>
                              <table class="table table-bordered" >
                                <thead>
                                  <tr>
                                    <th scope="col" width="10%">
                                      <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkall" >
                                        <label class="custom-control-label" for="checkall"></label>
                                    </div></th>
                                    <th scope="col" width="40%">Tên trạng thái</th>
                                    <th scope="col"width="15%">Ko doanh thu</th>
                                    <th scope="col"width="15%">Ko tiếp cận</th>
                                    <th scope="col"width="20%">Xóa</th>
                                  </tr>
                                </thead>
                                <tbody id="status-body">
                                  @foreach ($statusSystem as $item)
                                    <tr @if($item->is_customize == 0) data-system='1' @endif data-id = "{{ $item->id }}"  id="row{{ $item->id }}">
                                      <td>
                                        <div class="custom-control custom-checkbox">
                                          <input type="checkbox" class="checkRow custom-control-input" {{ $item->is_customize == 0 ? 'disabled' : '' }} id="checkbox{{$item->id}}" >
                                          <label class="custom-control-label" for="checkbox{{$item->id}}"></label>
                                        </div>
                                      </td>
                                      <td><input type="text"class="name" value="{{ $item->name }}" {{ $item->is_customize == 0 ? 'disabled' : '' }}></td>
                                      <td>
                                        <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input revenue"  {{ $item->is_customize == 0 ? 'disabled' : '' }} 
                                            {{ $item->no_revenue_flag == ACTIVE ? 'checked' : '' }} id="no_revenue_flag_{{$item->id}}" >
                                          <label class="custom-control-label" for="no_revenue_flag_{{$item->id}}"></label>
                                        </div>
                                      </td>
                                      <td>
                                        <div class="custom-control custom-checkbox">
                                          <input type="checkbox" class="custom-control-input  reach" {{ $item->is_customize == 0 ? 'disabled' : '' }} 
                                            {{ $item->no_reach_flag == ACTIVE ? 'checked' : '' }} id="no_reach_flag_{{$item->id}}">
                                          <label class="custom-control-label" for="no_reach_flag_{{$item->id}}"></label>
                                        </div>
                                      </td>
                                      <td class="text-center"> @if($item->is_customize == 0) 
                                            X
                                            @else
                                             <button class="btn btn-danger" onclick="removeRow({{ $item->id }})"><i class="fa fa-trash-alt"></i></button>
                                            @endif
                                      </td>
                                    </tr>
                                    
                                  @endforeach
                                </tbody>
                              </table>
                              <input type="hidden" name="removeStatus">
                              <button type="button" class="btn  btn-custom-warning " onclick="addRow()">Thêm</button>
                            </div>
                            <hr>
                            <div class="card bg-custom-warning">
                            <div class="card-header">
                                <h3 class="card-title">Chú thích:</h3>
                              <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                              </div>
                              <!-- /.card-tools -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" style="display: block;">
                              <ul class="">
                                <li class="">Chưa xác nhận: Là số mới chưa liên hệ, chưa xử lý</li>
                                <li class="">Không tiếp cận: là trạng thái không được tính là đã tiếp cận đến khách hàng</li>
                                <li class="">Không doanh thu: là trạng thái không tính doanh thu cho dù là đơn đó đã xác nhận</li>
                              </ul>
                            </div>
                            <!-- /.card-body -->
                          </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4 text-right">
                  <button type="button" class="btn btn-custom-color pull-right btn-save">Ghi lại</button>
                </div>
                
              </div>
            </div>
            
          <!-- /.card -->
        </div>
      </div>
    </form>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@stop
