@extends('layout.default')
@section('title') Admin | Khai báo kho @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Khai báo kho '),
'content' => [
__('Khai báo kho ') => route('admin.stock.warehouse.list')
],
'active' => [__('Khai báo kho ')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<script>
    @if($message = Session::get('error'))
        toastr.error('{{$message}}')
    @endif
    @if($message = Session::get('success'))
        toastr.success('{{$message}}')
    @endif
</script>
@stop

@section('content')
<section class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form
                    action="{{$mode == 'list' ? route('admin.stock.warehouse.list') : route('admin.stock.warehouse.delete')}}"
                    method="post" @if($mode=='delete' )
                    onsubmit="return confirm('Do you really want to submit the form?');" @endif>
                    @csrf
                    <div class="card">
                        <div class="card-header text-right">
                            @if($mode == 'list')
                            <a href="{{ route('admin.stock.warehouse.add') }}"><button type="button"
                                    class="btn btn-primary">Thêm mới</button></a>
                            @endif
                            <button type="submit" class="btn btn-danger">Xóa</button>
                            @if($mode == 'delete')
                            <a href="{{ route('admin.stock.warehouse.list') }}"><button type="button"
                                    class="btn btn-default">Quay lại</button></a>
                            @endif
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <table class="table">

                                    <thead>
                                        <tr valign="middle" bgcolor="#EFEFEF">
                                            <td width="1%" title="Chọn tất cả">
                                                <input type="checkbox" @if($mode=='delete' ) checked @endif value="1"
                                                    class="checkall" id="CrmCustomerGroup_all_checkbox">
                                            </td>
                                            <td nowrap="" align="left">
                                                <label for="CrmCustomerGroup_all_checkbox">Danh sách kho</label>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($listWarehouse as $warehouse)
                                        @if($mode == 'list' && $warehouse->is_default == ACTIVE)
                                        <tr class="group-item" bgcolor="white" valign="middle">
                                            <td></td>
                                            <td nowrap="" align="left"
                                                onclick="alert('Đây là kho mặc định của hệ thống. Bạn vui lòng không sửa chữa.')">
                                                <img src="/svg/spacer.gif" width="8">
                                                <img src="/svg/node.gif">
                                                <span class="page_indent">&nbsp;</span>
                                                {{$warehouse->name}}
                                            </td>
                                            <td width="24px" align="center">

                                            </td>
                                            <td width="24px" align="center">

                                            </td>
                                        </tr>
                                        @else
                                        <tr class="group-item" bgcolor="white" valign="middle">
                                            <td>
                                                <input name="selected_ids[]" @if($mode=='delete' ) checked @endif
                                                    type="checkbox" value="{{$warehouse->id}}" id="">
                                            </td>
                                            <td nowrap="" align="left"
                                                onclick="window.location='{{ route('admin.stock.warehouse.edit',$warehouse->id) }}'">
                                                -- <img src="/svg/tree_last.gif">
                                                <span class="page_indent">&nbsp;</span>
                                                {{$warehouse->name}} @if($warehouse->is_main == ACTIVE) <strong>(Kho bán
                                                    hàng)</strong> @endif
                                            </td>
                                            <td width="24px" align="center">
                                                <a href="#"><span class="fa fa-chevron-up"
                                                        aria-hidden="true"></span></a>
                                            </td>
                                            <td width="24px" align="center">
                                                <a href="#"><span class="fa fa-chevron-down"
                                                        aria-hidden="true"></span></a>
                                            </td>
                                        </tr>
                                        @endif

                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-warning-custom">
                                * Chú ý: Nếu không chọn kho nào là kho chính(bán hàng) thì <strong>Kho tổng</strong> sẽ
                                là kho chính(bán hàng)
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                </form>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </div><!-- /.container-fluid -->
</section>
@stop
