<div class="modal fade" id="modal-share-orders" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Chia đơn nhanh</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Phân loại sản phẩm</label>
                            <select class="custom-select" name="product_bundle" id="flash-share-bundle-id" onchange="updateNotAssignOrders()">
                                <option value="">Tất cả phân loại</option>
                                @foreach($productBundles as $key => $bundle)
                                    <option value="{{$bundle->id}}">{{$bundle->name}}</option>
                                @endforeach
                            </select>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Chọn nguồn</label>
                            <select class="custom-select" name="product_bundle" id="flash-share-source-id" onchange="updateNotAssignOrders()">
                                <option value="">Tất cả các nguồn</option>
                                @foreach($orderSources as $key => $source)
                                    <option value="{{$source->id}}">{{$source->name}}</option>
                                @endforeach
                            </select>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Số đơn cần chia</label>
                            <input type="text" class="form-control" id="flash-share-number-order" placeholder="Số đơn cần chia" value="2">
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning-custom">* Tổng <span id="total_not_assigned_order" style="color:#336699; font-weight: bold;">2</span> đơn chưa được gán sẽ tự động chia đều cho tất cả các tài khoản được đặt tùy chọn gán đơn.</div>
                <label><i class="fa fa-users"></i> Chọn sale (Giữ shift hoặc ctrl để chọn nhiều tài khoản)</label>
                <select id="all-ass-account-id" class="form-control" multiple="" data-toggle="tooltip" title="" style="height: 200px;width:100%;" data-original-title="Áp dụng với nhiều nhân viên">
                    <option value="">Chọn sale để gán đơn hàng</option>
                    @foreach($sales as $key => $sale)
                        <option value="{{$sale->id}}">{{$sale->name}}</option>
                    @endforeach
                </select>
                <div>
                    <label>Chọn nhóm tài khoản</label>
                    <select id="all-ass-group-id" class="form-control">
                        <option value="">Tất cả các tài khoản được quyền gán đơn</option>
                        @foreach($user_groups as $key => $group)
                            <option value="{{ $group->id }}"> {{ $group->name }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="flashShare()">Gán đơn</button>
            </div>
        </div>
    </div>
</div>