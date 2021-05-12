@extends('layout.default')
@section('title') Admin | Danh sách nhà cung cấp @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
  'title' => __('Danh sách nhà cung cấp'),
  'content' => [
  __('Danh sách nhà cung cấp') => route("admin.supplier.index")
],
'active' => [__('Danh sách nhà cung cấp')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script>
        var urlStore = '{{route('admin.supplier.save')}}';
        
    </script>
    <script src="{{ url("js/stock/stock_supplier.js") }}"></script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="card">
                        <form  id="base-information">
                            <div class="card-header">
                                <!-- /.modal -->
                                <div class="text-center" style="float: right">
                                <button  type="button" id="btn-save" class="btn btn-primary btn-custom-color float-right"><i class="fa fa-save mr-2"></i>Lưu</button>
                                </div>
                            </div>
                            <div id="alert-message-content" style="display:none;">
                            
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="removeSupplier" value="">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th width='10%'>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="" id="checkall" >
                                            <label class="" for="checkall"></label>
                                        </div>
                                        </th>
                                        <th width='15%'>Mã nhà cung cấp</th>
                                        <th width='20%'>Tên nhà cung cấp</th>
                                        <th width='15%'>Điện thoại</th>
                                        <th width='15%'>Địa chỉ</th>
                                        <th width='20%'>Tỉnh thành</th>
                                        <th width='5%'></th>
                                    </tr>
                                    </thead>
                                    <tbody id="supplier-body">
                                        @foreach ($supplier as $index => $item)
                                            <tr id="row{{$item->id}}" data-id="{{$item->id}}">
                                                <input type="hidden" class="supplier-id" name="data[{{$item->id}}][id]" value="{{$item->id}}">
                                                <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="" id="customCheck{{$item->id}}" >
                                                    <label class="" for="customCheck{{$item->id}}"></label>
                                                </div>
                                                </td>
                                                <td><input type="text" name="data[{{$item->id}}][code]" class="form-control supplier-code" id="" placeholder=""value="{{$item->code}}"></td>
                                                <td><input type="text" name="data[{{$item->id}}][name]" class="form-control" id="" placeholder=""value="{{$item->name}}"></td>
                                                <td><input type="text" name="data[{{$item->id}}][phone]" class="form-control" id="" placeholder=""value="{{$item->phone}}"></td>
                                                <td><input type="text" name="data[{{$item->id}}][address]" class="form-control" id="" placeholder="" value="{{$item->address}}"></td>
                                                <td>
                                                <select class="form-control" name="data[{{$item->id}}][prefecture]">
                                                    @foreach ($provinces as $province)
                                                        <option @if($item->prefecture == $province->id ) selected @endif value="{{$province->id}}">{{$province->_name}}</option>
                                                    @endforeach
                                                </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" onclick="removeRow({{$item->id}})" class="btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-custom-warning mt-3" onclick="addRow()">Thêm mới</button>
                            </div>
                        </form>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <div class="d-none" id="prefecture">
            @foreach ($provinces as $province)
                <option value="{{$province->id}}">{{$province->_name}}</option>
            @endforeach
    </div>
@stop
