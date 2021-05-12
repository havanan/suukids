<div class="modal fade" id="modal-province">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Chọn địa chỉ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select class="form-control select2" name="province_id" id="province-select">
                        <option></option>
                        @foreach($provinces as $key => $province)
                            <option value="{{$province->id}}" @if (!empty($data) && !empty($data->province) && $data->province->id == $province->id) selected @endif>{{$province->_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control select2" name="district_id" id="district-select">
                        @if (!empty($data) && !empty($data->district))
                            <option value="{{$data->district->id}}" selected> {{$data->district->_name}} </option>
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control select2" name="ward_id" id="ward-select">
                        @if (!empty($data) && !empty($data->ward))
                            <option value="{{$data->ward->id}}" selected> {{$data->ward->_name}} </option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"
                    onclick="onSaveAddress()">Lưu</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
