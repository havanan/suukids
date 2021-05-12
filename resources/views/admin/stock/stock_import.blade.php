@extends('layout.default')
@section('title') Admin | Nhập kho @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Nhập kho'),
            'content' => [
                __('Nhập kho') => route('stock.stock_out.import')
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
    <script>
        function addRow() {
            var d = new Date();
            var id = d.getTime();
            var row = $('<tr id="row'+id+'">' +
                '<td><input type="text" name="id" class="form-control"></td>' +
                '<td><input type="text" class="form-control" name="name"></td>' +
                '<td><input type="number" class="form-control qty" onChange="calAmount('+id+')"></td>' +
                '<td><input type="text" class="form-control"></td>' +
                '<td><input type="text" class="form-control" disabled></td>' +
                '<td><input type="number" class="form-control price" onChange="calAmount('+id+')"></td>' +
                '<td><input type="text" class="form-control amount" onChange="calTotalAmount()"></td>' +
                '<td><select class="form-control" name=""><option value="">Kho tổng</option></select></td>' +
                '<td><button class="btn btn-danger" onclick="removeRow('+id+')"><i class="fa fa-trash-alt"></i></button></td>' +
                '</tr>');

            $('#order-source-body').append(row);
        }
        function calAmount(id){
          let qty = $('#row'+ id).find('.qty').val()
          let price = $('#row'+ id).find('.price').val()
          $('#row'+ id).find('.amount').val(price * qty)
          calTotalAmount()
        }
        function calTotalAmount(){
          let totalAmount = 0;
          $('#addProduct > tbody  > tr').each(function() { 
            totalAmount += parseFloat($(this).find('.amount').val());
          });
          $('#total_amount').val(formatNumber(totalAmount))
        }
        function formatNumber(num) {
          return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
        function removeRow(id) {
            if(id){
                $('#row'+id).remove();
                calTotalAmount()
            }
        }
        function toogleSupplierSelect(){
            $('#supplier_select_bound').toggle('slow');
        }
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-right">
                            <button class="btn btn-default" data-toggle="modal" data-target="#modal-lg"><i class="fa fa-file-import mr-2"></i>Import Excel</button>
                            <a href="{{route('stock.stock_out')}}" class="btn btn-default" ><i class="fa fa-list mr-2"></i>Danh sách phiếu</a>
                            <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                        </div>
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
                                    <form method="post" action="" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="p-5 text-center">----------------Chọn file Excel-----------------</div>

                                            File mẫu: <a title="Tải về file mẫu" href="{{asset('theme/admin-lte/dist/file/excel_sp_mau.xlsx')}}"
                                                         target="_blank">
                                                <img src="{{asset('theme/admin-lte/dist/img/file_excel_sp_mau.png')}}"width="90%" alt="">
                                            </a>
                                            <hr>
                                            <div class="form-group">
                                                <input name="excel_file" type="file" id="excel_file" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer text-right">
                                            <button type="submit" class="btn btn-primary">Import</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td>Ngày tạo (*):</td>
                                    <td class="form-group has-feedback">
                                        <div class="input-group date">
                                            <input name="create_date" id="create_date" class="form-control" type="text"
                                                   value="04/03/2020" data-bv-field="create_date">
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-th"></span>
                                            </div>
                                        </div>
                                        <i class="form-control-feedback bv-no-label bv-icon-input-group"
                                           data-bv-icon-for="create_date" style="display: none;"></i>
                                        <small class="help-block" data-bv-validator="notEmpty" data-bv-for="create_date"
                                               data-bv-result="NOT_VALIDATED" style="display: none;">Bạn phải nhập
                                            ngày</small></td>
                                    <td align="right"><span>Số phiếu (*):</span></td>
                                    <td class="form-group" align="right"><input name="bill_number" id="bill_number"
                                                                                class="form-control" type="text"
                                                                                value="PX01"></td>
                                </tr>
                                <tr>
                                    <td>Người giao:</td>
                                    <td class="form-group"><input name="deliver_name" id="deliver_name"
                                                                  class="form-control" type="text" value=""></td>
                                    <td align="right"><span>Người nhận:</span></td>
                                    <td align="right" class="form-group"><input name="receiver_name" id="receiver_name"
                                                                                class="form-control" type="text"
                                                                                value=""></td>
                                </tr>
                                <tr valign="top">
                                    <td>Diễn giải:</td>
                                    <td colspan="3"><textarea name="note" id="note" class="form-control"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <fieldset>
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td align="right" width="30%">Trả lại nhà cung cấp
                                            <input name="get_back_supplier" id="get_back_supplier" value="1"
                                                   onclick="toogleSupplierSelect()"
                                                   type="checkbox"></td>
                                        <td align="left">
                                            <div id="supplier_select_bound" style="display: none;">
                                                <label for="supplier_id">Supplier:
                                                    <select name="supplier_id" id="supplier_id" class="form-control">
                                                        <option value="">Chưa có nhà cung cấp</option>
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
                                    <th >Tên sản phẩm</th>
                                    <th class="w-10p">Số lượng</th>
                                    <th class="w-10p">HSD</th>
                                    <th class="w-10p">Đơn vị</th>
                                    <th>Giá</th>
                                    <th>Thành tiền</th>
                                    <th>Kho</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="order-source-body">

                                </tbody>
                            </table>
                            <button class="btn  btn-custom-warning mt-3" onclick="addRow()">+ Thêm sản phẩm</button>
                            <div class="alert alert-warning-custom">
                                Nhập tối đa 400 sản phẩm
                            </div>
                            <div class="text-right">
                                <strong>Tổng thanh toán: </strong>
                                <input type="text" value="0" readonly id="total_amount">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@stop
