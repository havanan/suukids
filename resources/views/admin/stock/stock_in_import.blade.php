@extends('layout.default')
@section('title') Admin | Nhập kho @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Nhập kho'),
'content' => [
__('Nhập kho') => route('admin.stock.stock_in_import')
],
'active' => [__('Nhập kho')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ url('css/source.css') }}">
<link rel="stylesheet" href="{{url('theme/admin-lte/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
<script src="{{url('theme/admin-lte/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script>
    const urlStore = "{{ route('admin.stock.stock_in_store') }}";
    const urlList = "{{ route('admin.stock.stock_in_list') }}";
    const urlGetProduct = "{{ route('admin.stock.stock_in_get_product') }}";
    const stockGroups =  JSON.parse('{!! json_encode($stockGroups) !!}');
    const urlUpload = "{{route('admin.stock.stock_in.inportExcel')}}";
    const colorList = JSON.parse('<?php echo json_encode(COLORS) ?>');
    const sizeList = JSON.parse('<?php echo json_encode(SIZES) ?>');
    const unitList = JSON.parse('<?php echo json_encode($units) ?>');

    // $(document).ready(function(){
    //     $(".product-expired-date").datepicker({ format: "dd/mm/yyyy",autoclose: true,todayHighlight:'TRUE', });
    // });
</script>
<script src="{{ url("js/stock/stock_in_import.js") }}"></script>
@stop

@section('content')
<section class="content content-stock">
    <div class="container-fluid">
        <form id="base-information">
            @csrf
            @if(!empty($entity->id))
            <input type="hidden" name="id" value="{{ $entity->id }}">
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-right">
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-lg"><i
                                    class="fa fa-file-import mr-2"></i>Import Excel</button>
                            <a href="{{route('admin.stock.stock_in_list')}}" class="btn btn-default"><i
                                    class="fa fa-list mr-2"></i>Danh sách phiếu</a>
                            <button id="btn-save" type="button" class="btn btn-primary"><i
                                    class="fa fa-save mr-2"></i>Lưu
                                lại</button>
                        </div>
                        <div id="alert-message-content" style="display:none;">
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Ngày tạo (*):</td>
                                        <td class="form-group has-feedback">
                                            <div class="input-group date">
                                                <input name="create_day" id="create_date" class="form-control"
                                                    type="text" value="{{ date("d/m/Y") }}" data-bv-field="create_date">
                                                <div class="input-group-addon">
                                                    <span class="glyphicon glyphicon-th"></span>
                                                </div>
                                            </div>
                                            <i class="form-control-feedback bv-no-label bv-icon-input-group"
                                                data-bv-icon-for="create_date" style="display: none;"></i>
                                            <small class="help-block" data-bv-validator="notEmpty"
                                                data-bv-for="create_date" data-bv-result="NOT_VALIDATED"
                                                style="display: none;">Bạn phải nhập
                                                ngày</small>
                                        </td>
                                        <td align="right"><span>Số phiếu (*):</span></td>
                                        <td class="form-group" align="right"><input name="bill_number" id="bill_number"
                                                class="form-control" type="text" value="{{ $billNumber }}"></td>
                                    </tr>
                                    <tr>
                                        <td>Người giao:</td>
                                        <td class="form-group">
                                            <input name="deliver_name" id="deliver_name" class="form-control"
                                                type="text" value="{{ $entity->deliver_name }}">
                                        </td>
                                        <td align="right"><span>Người nhận:</span></td>
                                        <td align="right" class="form-group">
                                            <input name="receiver_name" id="receiver_name" class="form-control"
                                                type="text" value="{{ $entity->receiver_name }}">
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td>Diễn giải:</td>
                                        <td colspan="3">
                                            <textarea name="note" id="note"
                                                class="form-control">{{ $entity->note }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <fieldset>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td align="right" width="30%">Nhà cung cấp:
                                                <input onclick="toogleSupplierSelect()" id="supplier_select"
                                                    type="checkbox"
                                                    {{ !empty($entity->supplier_id) ? "checked" : ""  }}></td>
                                            <td align="left">
                                                <div id="supplier_select_bound" style="display: none;">
                                                    <label for="supplier_id">Supplier:
                                                        <select name="supplier_id" id="supplier_id"
                                                            class="form-control">
                                                            @if(empty($suppliers))
                                                                <option value="">Chưa có nhà cung cấp</option>
                                                            @else
                                                                <option value="">Chọn nhà cung cấp</option>
                                                            @endif
                                                            @foreach ($suppliers as $id => $supplierName)
                                                            <option value="{{ $id }}"
                                                                {{ $entity->supplier_id == $id ? "selected" : "" }}>
                                                                {{ $supplierName  }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>

            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card box-info">
                        <div class="card-header card-header-blue">
                            <h3 class="card-title text-white">
                                Sản phẩm hàng hoá
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered" id="addProduct">
                                <thead>
                                    <tr>

                                        <th class="w-15p">Mã SP</th>
                                        <th>Tên sản phẩm</th>
                                        <th class="w-10p">Số lượng</th>
                                        <th class="w-10p">Màu</th>
                                        <th class="w-10p">Size</th>
                                        <th class="w-10p">Đơn vị</th>
                                        <th>Giá</th>
                                        <th>Thành tiền</th>
                                        <th>Kho</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="order-source-body">
                                    @if (!empty($stockProducts))
                                        @foreach ($stockProducts as $item)
                                        <tr class="product-item">
                                            <input type="hidden" name="product_id[]" value="{{ $item->product->id }}" class="product-id">
                                            <input type="hidden" name="product_unit_id[]" value="{{ $item->product->unit_id }}" class="product-unit-id">
                                            <td>
                                                <input type="text" name="product_code[]" value="{{ $item->product->code }}" class="form-control product-code">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control product-name" value="{{ $item->product->name }}" name="product_name[]">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control product-quantity" value="{{ $item->quantity }}" name="product_quantity[]" min="0">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control product-color" value="{{ $item->product->color }}" name="product_color[]">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control product-size" value="{{ $item->product->size }}" name="product_size[]">
                                            </td>
                                            <td>
                                                <input type="text" name="product_unit_name[]" value="{{ $item->unit_name }}" class="form-control product-unit-name" readonly="">
                                            </td>
                                            <td>
                                                <input type="number" name="product_price[]" value="{{ $item->price }}" min="0" class="form-control product-price">
                                            </td>
                                            <td>
                                                <input type="text" name="product_total[]" value="{{ $item->total }}" value="0" class="form-control product-total" readonly="">
                                            </td>
                                            <td>
                                                <select class="form-control product-stock-group" value="{{ $item->stock_group_id }}" name="product_stock_group_id[]">
                                                    @foreach ($stockGroups as $idWarehouse => $warehouse)
                                                        <option value="{{ $idWarehouse }}" {{ $idWarehouse == $item->stock_group_id ? "selected" : "" }}>{{ $warehouse }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-remove-product-item"><i class="fa fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn  btn-custom-warning mt-3" onclick="addRow()">+ Thêm sản
                                phẩm</button>
                            <div class="alert alert-warning-custom">
                                Nhập tối đa 400 sản phẩm
                            </div>
                            <div class="text-right">
                                <strong>Tổng thanh toán: </strong>
                                <input type="hidden" value="0" name="total" id="total_amount_value">
                                <input type="text" value="0" id="total_amount" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div><!-- /.container-fluid -->
    <!-- /.popup import -->
    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import sẩn phẩm từ Excel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-5 text-center">----------------Chọn file
                        Excel-----------------
                    </div>
                    File mẫu: <a title="Tải về file mẫu"
                        href="{{asset('theme/admin-lte/dist/file/excel_sp_mau.xlsx')}}"
                        target="_blank">
                        <img src="{{asset('theme/admin-lte/dist/img/file_excel_sp_mau.png')}}"
                            width="90%" alt="">
                    </a>
                    <hr>
                    <div class="form-group">
                        <input type="file" id="excel_file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-primary"id="btn-upoad">Import</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</section>
@stop
