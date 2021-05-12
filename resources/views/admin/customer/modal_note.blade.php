<div class="modal fade" id="modelNote" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-right" >
                <div class="row w-100">
                    <div class="col-md-6 text-left">
                        <strong>Quản lý ghi chú</strong>
                    </div>
                    <div class="col-md-6" style="justify-content: flex-end;">
                        <button type="button" data-type = "0" id="saveNote" class="btn btn-primary mr-2"> <i class="fas fa-save"></i> Lưu</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-sign-out-alt"></i> Đóng lại</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card ">

                                <div class="card-header" style="color: #31708f;background-color: #d9edf7;border-color: #bce8f1;">
                                    Ghi chú:
                                </div>
                                <div class="card-body">
                                    <form class="form" role="form" autocomplete="off" id="formNote">
                                        <input type="hidden" name="note_customer_id" class="note_customer_id">
                                        <input type="hidden" name="note_create_by_id" class="note_create_by_id">
                                        <input type="hidden" name="note_customer_emotions_name" class="note_customer_emotions_name">
                                        <div class="form-group">
                                            <label for="uname1">Mã ghi chú</label>
                                            <input type="text" class="form-control note_id" name="note_id" readonly placeholder="auto">
                                        </div>
                                        <div class="form-group">
                                            <label for="uname1">Tên khách hàng</label>
                                            <input type="text" class="form-control note_customer_name" name="note_customer_name" disabled placeholder="auto">
                                        </div>
                                        <div class="form-group">
                                            <label>Cảm xúc khách hàng:</label>
                                            <select name="note_customer_emotions" class="form-control note_customer_emotions">
                                                @foreach (CUSTOMER_EMOTIONS as $index => $item)
                                                    <option value="{{$index}}">{{$item}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nội dung::</label>
                                            <textarea name="note_content" class="form-control note note_content" cols="30" rows="10"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Tạo lúc</label>
                                            <input name="note_date_create" type="text" class="form-control note_date_create" readonly value="{{date('Y-m-d H:i:s')}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Người tạo:</label>
                                            <input type="text" class="form-control note_create_by" name="note_create_by" readonly>
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
                                <div class="card-body" id="history_note" style="min-height:100px">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
