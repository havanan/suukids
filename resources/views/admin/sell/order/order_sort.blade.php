<div class="modal fade" id="modal-orders-sort" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tùy chọn cột hiển thị và xuất excel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if(getCurrentUser()->isAdmin())
                    <ul id="order_sortable">
                        @foreach ($sortDefault as $item)
                            <li class="ui-state-default">
                                <input type="checkbox" class="stl-row order-sort-item" data-field="{{ $item->name }}" {{ $item->show ? 'checked="checked"' : '' }}>
                                <i class="fa fa-align-justify"></i> <span>{{ $item->text }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="form-group mx-sm-3 mb-2 text-center">
                        <button type="button" class="btn btn-primary mb-2 width-150 save-order-sort-btn">Lưu lại</button>
                        <button type="button" class="btn btn-default mb-2 width-150" data-dismiss="modal">Đóng</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
