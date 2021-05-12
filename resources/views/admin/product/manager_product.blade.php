@extends('layout.default')
@section('title') Admin | Quản lý sản phẩm @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý sản phẩm'),
            'content' => [
                __('Quản lý sản phẩm') => route('admin.manager.products')
            ],
            'active' => [__('Quản lý sản phẩm')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)

@stop
{{-- End Breadcrumb --}}

@section('assets')
<script>
  var productUnit =  JSON.parse('{!! json_encode($productUnit) !!}');
  var productBundle =  JSON.parse('{!! json_encode($productBundle) !!}');
  var imgNoProduct = '{{url("theme/admin-lte/dist/img/no_product_image.png")}}';
  var urlConfirm = "{{ route('admin.manager.products.delete') }}";
  var urlStore = "{{ route('admin.manager.products.store') }}";
  var urlValidateBeforeSave = "{{ route('admin.manager.products.validateBeforeSave') }}";
</script>
@include('layout.flash_message')
<script src="{{ url("js/products/index.js") }}"></script>
<style>
  table td {
    position: relative;
    text-align: center!important; /* center checkbox horizontally */
    vertical-align: middle!important; /* center checkbox vertically */
  }
  table td input {
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
  .img-product-size{
    height: 60px;
    width: 60px;
  }
  #swal2-content{
    white-space: pre-line;
  }
</style>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Editable table -->
                    <div class="card">
                      <div class="card-header text-right d-flex justify-content-end">
                        <form action="{{route('admin.manager.products')}}" method="get" style="display:contents">
                          <input type="text" name= "search" class="form-control w-25 mr-1" value="{{ request()->get('search') }}">
                          <button type="submit" class="btn btn-default mr-1"><i class="fas fa-search"></i>Tìm kiếm</button>
                        </form>
                        <button type="button" class="btn btn-default mr-1" data-toggle="modal" data-target="#modal-lg"><i
                                class="fa fa-file-import mr-2"></i>Import Excel</button>
                        <a href="{{route('admin.manager.exportExcel')}}" class="btn btn-default mr-1">
                          <i class="fas fa-cloud-download-alt"></i>Export Excel</a>
                        <button id="btn-save" type="button" class="btn btn-primary"><i
                                class="fa fa-save mr-2"></i>Lưu
                            lại</button>
                      </div>
                      <div class="card-body">
                        <div class="row" style="margin:0">
                          <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                              <a class="nav-item nav-link {{ request()->get('type') != 1 ? 'active': '' }}" href="{{route('admin.manager.products')}}">Danh sách sản phẩm hàng hóa</a>
                              <a class="nav-item nav-link text-danger {{ request()->get('type') == 1 ? 'active': '' }}" href="{{route('admin.manager.products',['type'=>1])}}">Sản phẩm ngừng kinh doanh(Ẩn)</a>
                          </div>
                        </div>
                        <form id='form-products' enctype='multipart/form-data' method="post" action="{{ route('admin.manager.products.store') }}">
                          @csrf
                          <div id="table" class="table-editable">
                            <table class="table table-bordered table-hover">
                              <thead>
                                <tr>
                                  <th class="text-center">Ảnh</th>
                                  <th class="text-center">Mã SP</th>
                                  <th class="text-center">Tên sản phẩm</th>
                                  <th class="text-center">Giá bán</th>
                                  <th class="text-center">Giá vốn</th>
                                  <th class="text-center">K.L (Gram)</th>
                                  <th class="text-center">Mầu</th>
                                  <th class="text-center">Size</th>
                                  <th class="text-center">Phân Loại</th>
                                  <th class="text-center">Đơn vị</th>
                                  <th class="text-center">Số ĐH</th>
                                  <th class="text-center text-danger">Ẩn</th>
                                  <th class="text-center">Xóa</th>
                                </tr>
                              </thead>
                              <tbody id="product-body">
                                @foreach ($productList as $item)
                                  <tr class="product-item" id="pro_{{$item->id}}">
                                    <input type="hidden" name="edit[{{$item->id}}][id]" value="{{$item->id}}">
                                    <td>
                                      <div>
                                        <img src="{{ $item->product_image ? url($item->product_image) : url("theme/admin-lte/dist/img/no_product_image.png") }}" class="img-product-size"><br>
                                        <i class="fas fa-upload upload-img-pro" style="cursor: pointer;"></i>
                                        <input type="file" accept="image/*" onchange="readURL(this)" name="edit[{{$item->id}}][product_image]" class="w-25 d-none">
                                      </div>
                                    </td>
                                    <td><input type="text" name="edit[{{$item->id}}][code]" value="{{$item->code}}"></td>
                                    <td><input type="text" name="edit[{{$item->id}}][name]" value="{{$item->name}}"></td>
                                    <td><input type="text" name="edit[{{$item->id}}][price]" value=" {{$item->price}}"></td>
                                    <td><input type="text" name="edit[{{$item->id}}][cost_price]" value="{{$item->cost_price}}"></td>
                                    <td><input type="text" name="edit[{{$item->id}}][weight]" value="{{$item->weight}}"></td>
                                    <td><input type="text" name="edit[{{$item->id}}][color]" value="{{$item->color}}"></td>
                                    <td><input type="text" name="edit[{{$item->id}}][size]" value="{{$item->size}}"></td>
                                    <td>
                                      <select name="edit[{{$item->id}}][bundle_id]" class="form-control">
                                        @foreach ($productBundle as $key => $bundle)
                                          <option {{$item->bundle_id == $key ? 'selected' : ''}} value="{{$key}}">{{$bundle}}</option>
                                        @endforeach
                                      </select>
                                    </td>
                                    <td>
                                      <select name="edit[{{$item->id}}][unit_id]" class="form-control">
                                          @foreach ($productUnit as $key => $unit)
                                          <option {{$item->unit_id == $key ? 'selected' : ''}}  value="{{$key}}">{{$unit}}</option>
                                          @endforeach
                                      </select>
                                    </td>
                                    <td class="">{{ $item->count_order }}</td>
                                    <td class="">
                                      <div class="custom-control custom-checkbox">
                                      <input type="checkbox" {{$item->status == STOP_BUSINESS ? 'checked' : ''}}
                                        class="checkRow custom-control-input" name="edit[{{$item->id}}][status]" id="checkbox{{$item->id}}" >
                                        <label class="custom-control-label" for="checkbox{{$item->id}}"></label>
                                      </div>
                                    </td>
                                    <td>
                                      <span class="table-remove" onClick='alertConfirm({{$item->id}})'><button type="button"
                                          class="btn btn-danger btn-rounded btn-sm my-0"><i class="far fa-trash-alt"></i></button></span>
                                    </td>
                                  </tr>
                                @endforeach

                              </tbody>
                            </table>
                            {{ $productList->links() }}
                          </div>
                        </form>
                        <div>
                          (<span class="count-product">{{count($productList)}}</span> sản phẩm) /
                          <input type="button" value="+ Thêm sản phẩm" class="btn btn-warning btn-sm" onclick="addRow()">
                          (Sau khi thêm bạn nhớ nhấn nút <strong>Lưu</strong>)</div>
                      </div>

                      <div class="card bg-custom-warning ml-4 mr-4">
                        <div class="card-header">
                            <h3 class="card-title">Diễn giải:</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                            </button>
                          </div>
                          <!-- /.card-tools -->
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" style="display: block;">
                          <ul class="">
                            <li class=""><strong>Số ĐH</strong>: Số lượng đơn hàng đã thêm sản phẩm không phân biệt trạng thái đơn hàng.</li>
                            <li class="">Chỉ <span class="badge badge-danger">Xóa</span> được sản phẩm khi Số ĐH = 0</li>
                            <li class="text-danger">Tính năng ẩn giúp ẩn sản phẩm khỏi danh sách chọn khi tạo đơn hàng hay ngừng kinh doanh.</li>
                          </ul>
                        </div>
                        <!-- /.card-body -->
                      </div>
                    </div>
                    <!-- Editable table -->
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->

        <!-- /.popup import -->
        <!-- /.popup import -->
        @include('admin.product.import_form')
        <!-- /.modal -->
      <!-- /.modal -->
    </section>
@stop
