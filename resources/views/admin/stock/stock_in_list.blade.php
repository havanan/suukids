@extends('layout.default')
@section('title') Admin | Quản lý nhập kho @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Quản lý nhập kho'),
'content' => [
__('Quản lý nhập kho') => ''
],
'active' => [__('Quản lý nhập kho')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<script>
    var urlDelete = "{{ route('admin.stock_in.delete') }}";
</script>
<script src="{{ url("js/stock/stock_list.js") }}"></script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- /.modal -->
                        <div class="text-center" style="float: right">
                            <a href="{{ route('admin.stock.stock_in_import') }}" class="btn btn-primary">+ Thêm
                                phiếu</a>
                                <a href="{{route('admin.stock.stock_out_move_product')}}"><button class="btn btn-success">Xuất nội bộ</button></a>
                            <button class="btn btn-danger" onclick="alertConfirm()">Xóa</button>
                        </div>

                    </div>
                    <div id="alert-message-content" style="display:none;">

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="{{route('admin.stock.stock_in_list')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-1">
                                    <label>Số phiếu:</label>
                                    <input name="bill_number" id="bill_number" class="form-control w-90" type="text"
                                        value="{{isset($dataSearch['bill_number']) ? $dataSearch['bill_number'] : ''}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Diễn giải:</label>
                                    <input name="note" id="note" class="form-control w-90" type="text"
                                        value="{{isset($dataSearch['note']) ? $dataSearch['note'] : ''}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Từ ngày:</label>
                                    <input name="create_date_from" id="create_date_from" class="form-control w-90 datepicker"
                                        type="text" value="{{isset($dataSearch['create_date_from']) ? $dataSearch['create_date_from'] : ''}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>đến ngày:</label>
                                    <input name="create_date_to" id="create_date_to" class="form-control w-90 datepicker" type="text"
                                        value="{{isset($dataSearch['create_date_to']) ? $dataSearch['create_date_to'] : ''}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Nhà cung cấp:</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control w-90">
                                        <option value="">Tất cả</option>
                                        @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}"
                                            {{isset($dataSearch['supplier_id']) && $dataSearch['supplier_id'] == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Kho:</label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-control w-90">
                                        <option value="">Tất cả</option>
                                        @foreach ($stockGroups as $item)
                                        <option value="{{$item->id}}"
                                        {{isset($dataSearch['warehouse_id']) && $dataSearch['warehouse_id'] == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>.</label><br>
                                    <button type="submit" name="search" class="btn btn-default btn-custom-padd">
                                        <i class="fa fa-search"></i> Tìm
                                    </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <table class="table table-bordered" width="100%" id="stock-table">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkall">
                                            <label class="custom-control-label" for="checkall"></label>
                                        </div>
                                    </th>
                                    <th width="1%">#</th>
                                    <th>Ngày tạo</th>
                                    <th width="10%" align="left">Số phiếu</th>
                                    <th width="10%" align="left">Người xuất</th>
                                    <th width="10%" align="left">Người nhận</th>
                                    <th align="left">Diễn giải</th>
                                    <th width="20%" align="left">Nhà cung cấp</th>
                                    <th align="left">Số lượng</th>
                                    <th width="10%" align="center">Tổng tiền</th>
                                    <th width="1%">&nbsp;</th>
                                    <th width="1%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalAmount = 0; @endphp
                                @foreach ($stockIn as $index => $item)
                                  @php $totalAmount += ($item->total * $item->stockProduct->quantity); @endphp
                                  <tr>
                                    <td>
                                      <div class="custom-control custom-checkbox">
                                        <input type="checkbox" value="{{$item->id}}" class="custom-control-input checkDel" id="customCheck{{$item->id}}">
                                        <label class="custom-control-label" for="customCheck{{$item->id}}"></label>
                                      </div>
                                    </td>
                                    <td>{{$index += 1}}</td>
                                    <td>{{$item->create_day}}</td>
                                    <td>{{$item->bill_number}}</td>
                                    <td>{{$item->deliver_name}}</td>
                                    <td>{{$item->receiver_name}}</td>
                                    <td>{{$item->note}}</td>
                                    <td>{{isset($item->supplier->name) ? $item->supplier->name : ''}}</td>
                                    <td>{{number_format($item->stockProduct->quantity)}}</td>
                                    <td>{{number_format($item->total)}}</td>
                                    <td><a href="{{route('admin.stock.stock_in.view',$item->id)}}"><button type="button" class="btn btn-block bg-gradient-secondary">Xem</button></a></td>
                                    <td nowrap="nowrap"><a href="{{route('admin.stock.stock_in_edit',$item->id)}}"><button type="button" class="btn btn-block btn-warning">Sửa</button></a></td>
                                  </tr>
                                @endforeach
                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="7" align="right" style="color:#F00;font-weight:bold;">Tổng tiền</td>
                                    <td align="right" style="color:#F00;font-weight:bold;">{{number_format($totalAmount)}}</td>
                                    <td>&nbsp;</td>
                                    <td nowrap="nowrap">&nbsp;</td>
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
