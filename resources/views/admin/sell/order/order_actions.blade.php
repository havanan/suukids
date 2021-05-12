<div class="modal fade" id="modal-orders-actions" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thao tác với đơn hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if(getCurrentUser()->hasPermission('share_orders') || getCurrentUser()->isGroupLeader())
                <!-- .actions-tab -->
                <ul class="nav nav-tabs mb-3" id="custom-content-below" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-content-below-assign-sales-tab" data-toggle="pill"
                            href="#custom-content-below-assign-sales" role="tab" aria-controls="custom-content-below-assign-sales"
                            aria-selected="true">Chia đơn cho Sale</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-content-below-assign-mkt-tab" data-toggle="pill"
                            href="#custom-content-below-assign-mkt" role="tab" aria-controls="custom-content-below-assign-mkt"
                            aria-selected="false">Gán cho MKT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-content-below-assign-group-tab" data-toggle="pill"
                            href="#custom-content-below-assign-group" role="tab" aria-controls="custom-content-below-assign-group"
                            aria-selected="false">Theo nhóm</a>
                    </li>
                </ul>
                <!-- /.actions-tab -->
                <!-- .actions-content -->
                <div class="tab-content" id="custom-content-below-tabContent">
                    <div class="tab-pane fade show active" id="custom-content-below-assign-sales" role="tabpanel"
                        aria-labelledby="custom-content-below-assign-sales-tab">
                        <div class="input-group mb-3">
                            <select id="actions-sale-id-select" class="form-control">
                                <option value="">Chọn sale để gán đơn hàng</option>
                                <option value="-1">Bỏ chia / Không chia</option>
                                @foreach($sales as $key => $sale)
                                    <option value="{{$sale->id}}">{{$sale->name}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="assignOrderForSale()">Thực hiện</button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-content-below-assign-mkt" role="tabpanel"
                        aria-labelledby="custom-content-below-assign-mkt-tab">
                        <div class="input-group mb-3">
                            <select id="actions-mkt-id-select" class="form-control">
                                <option value="">Chọn mkt để gán đơn hàng</option>
                                @foreach($marketings as $key => $marketing)
                                    <option value="{{$marketing->id}}">{{$marketing->name}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="assignOrderForMarketing()">Thực hiện</button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-content-below-assign-group" role="tabpanel"
                        aria-labelledby="custom-content-below-assign-group-tab">
                        <div class="input-group mb-3">
                            <select id="ass_account_group" class="form-control" name="ass_account_group">
                                <option value="">Chọn nhóm</option>
                                @foreach($user_groups as $key => $group)
                                <option value="{{ $group->id }}"> {{  $group->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" onclick="assignOrderForGroup()">Thực hiện</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.actions-content -->
                @endif
            </div>
            <div class="modal-footer justify-content-between">
                <div class="order-action-group-btn pt-5 w-100">
                    <div class="row">
                        <div class="col-4">
                            @if(getCurrentUser()->hasPermission('export_excel'))
                            <button type="button" class="btn btn-success width-200" onclick="showExcelModal()">
                                <i class="fa fa-download"></i> Xuất Excel
                            </button>
                            @endif
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn btn-default width-200" onclick="showUpdateStatusModal()">
                                <i class="fas fa-sync-alt"></i> Chuyển trạng thái
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-default width-200">
                                <i class="fas fa-share"></i> Xuất kho
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            @if (getCurrentUser()->hasPermission('delete_orders'))
                                <button type="button" class="btn btn-warning width-200" onclick="deleteOrders()">
                                    <i class="fa fa-trash"></i> Xóa <span class="number-order-select badge">0</span> đơn
                                </button>
                            @endif
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-danger width-200" data-dismiss="modal">
                                <i class="fa fa-minus-circle"></i> Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
