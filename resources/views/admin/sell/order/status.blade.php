<div class="modal fade" id="modal-status" aria-modal="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Chọn trạng thái</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="order-status-row">
                    @foreach($statuses as $key => $status)
                        <div class="col-2">
                            <div class="step-ward-arrow">Level {{$key}}</div>
                            <div class="step-ward-box">
                                <div class="step-ward-item">
                                    @foreach($status as $key2 => $item)
                                    <div class="form-check">
                                        <input class="form-check-input status-checkbox" type="radio" value={{$item->id}} data-send-ems='{{ $item->id == DELIVERY_ORDER_STATUS_ID }}' data-name='{{ $item->name }}' data-color='{{ $item->color }}'  name="status_id" type="checkbox" 
                                        @if(empty($isIndexPage) && !empty($data) && !empty($data->status) && $data->status->id == $item->id) checked @elseif(empty($isIndexPage) && $key == 0 && $key2  ==0) checked @endif id="checkbox-status-{{$item->id}}">
                                        <i style="width: 10px; height:10px; background: {{$item->color}}; display:inline-block"></i>
                                        <label style="font-size: 14px" for="checkbox-status-{{$item->id}}" class="form-check-label"> {{$item->name}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>