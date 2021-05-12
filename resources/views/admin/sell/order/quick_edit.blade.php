<div class="modal fade" id="modal-quick-edit" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Sửa nhanh đơn hàng <span id="quick-edit-order-code">ABCD</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body-quick-edit">
                <input type="hidden" id="quick-edit-id" value="">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            Họ và tên: <span id="quick-edit-customer-name"></span> <br>
                            Điện thoại: <span id="quick-edit-customer-phone"></span> <br>
{{--                            Địa chỉ: <span id="quick-edit-address"></span> <br>--}}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            Tổng tiền: <span id="quick-edit-price"></span><br>
{{--                            Giảm giá: <span id="quick-edit-discount-price"></span><br>--}}
                            Thành tiền: <span class="text-bold text-success" id="quick-edit-total-price">0</span><br>
                        </div>
                    </div>
                </div>
                <form id="frm-product-body">
                    <table class="table table-bordered" id="tbl-product">

                    </table>
                    <table class="table table-bordered" id="tbl-info">
                    <tbody>
                        <tr>
                            <th style="width: 25%"></th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>Giảm giá</td>
                            <td><input type="text" id="quick-edit-discount-price" data-old="0" class="form-control price" onchange="updateQuickProductPrice()"></td>
                        </tr>
                        <tr>
                            <td>Địa chỉ</td>
                            <td><textarea id="quick-edit-address" class="form-control"></textarea></td>
                        </tr>
                        <tr>
                            <td>Ghi chú chung</td>
                            <td><textarea id="quick-edit-note1" class="form-control"></textarea></td>
                        </tr>
                        <tr>
                            <td>Ghi chú 2</td>
                            <td><textarea id="quick-edit-note2" class="form-control"></textarea></td>
                        </tr>
                    </tbody>
                </table>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default show-order-history-modal" onclick="showOrderHistoryModal()"><i class="far fa-clock"></i> Lịch sử đơn hàng</button>
                <div>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="flashUpdateOrder()">Cập nhật</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
