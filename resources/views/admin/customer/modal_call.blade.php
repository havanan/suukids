<div class="modal fade" id="modelCall" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-right" style="justify-content: flex-end;">
                <button type="button" id="saveCall" class="btn btn-primary mr-2"> <i class="fas fa-save"></i> Lưu</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-sign-out-alt"></i> Đóng lại</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card ">

                                <div class="card-header" style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;">
                                    Cuộc gọi:
                                </div>
                                <div class="card-body">
                                    <form class="form" role="form" autocomplete="off" id="formCall">
                                        <input type="hidden" name="call_customer_id" class="call_customer_id">
                                        <input type="hidden" name="call_create_by_id" class="call_create_by_id">
                                        <input type="hidden" name="call_customer_emotions_name" class="call_customer_emotions_name">
                                        <div class="form-group">
                                            <label for="uname1">Mã cuộc gọi</label>
                                            <input type="text" class="form-control call_id" name="call_id" readonly placeholder="auto">
                                        </div>
                                        <div class="form-group">
                                            <label>Trạng thái (CSKH):</label>
                                            <select class="form-control call_customer_care_id" name="call_customer_care_id">
                                                @foreach (CUSTOMER_CARE as $index => $item)
                                                    <option value="{{$index}}">{{$item}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nội dung::</label>
                                            <textarea name="call_content" class="form-control note call_content" cols="30" rows="10"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Cảm xúc khách hàng:</label>
                                            <select name="call_customer_emotions" class="call_customer_emotions form-control">
                                                @foreach (CUSTOMER_EMOTIONS as $index => $item)
                                                    <option value="{{$index}}">{{$item}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Thời Gian</label>
                                            <input name="call_date_create" type="text" class="form-control call_date_create" readonly value="{{date('Y-m-d H:i:s')}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Tên khách hàng</label>
                                            <input type="text" class="form-control call_customer_name" name="call_customer_name" readonly>
                                        </div>
                                    </form>
                                </div>
                                <!--/card-block-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="far fa-clock mr-2"></i>Lịch sử
                                </div>
                                <div class="card-body" id="history_call" style="min-height:100px">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
