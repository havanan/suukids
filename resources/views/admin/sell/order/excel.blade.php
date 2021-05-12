<div class="modal fade" id="modal-excel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Xuất excel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <select class="form-control" id="export-type-select">
                    <option value="order">Theo đơn hàng</option>
                    <option value="product">Theo sản phẩm</option>
                  </select>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="row w-100">
                    <div class="col-6">
                        <button type="button" class="btn btn-danger w-100" data-dismiss="modal" onclick="exportExcelOrders(true)">Xuất tất cả</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-success w-100" data-dismiss="modal" onclick="exportExcelOrders(false)">Xuất đơn được chọn</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
