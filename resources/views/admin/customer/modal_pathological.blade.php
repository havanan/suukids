<div class="modal fade" id="modelPathological" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header text-right" >
              <div class="row w-100">
                  <div class="col-md-6 text-left">
                      <strong>Quản lý bệnh lý</strong>
                  </div>
                  <div class="col-md-6" style="justify-content: flex-end;">
                      <button type="button" data-type = "0" id="savePathological" class="btn btn-primary mr-2"> <i class="fas fa-save"></i> Lưu</button>
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
                                  Bệnh lý:
                              </div>
                              <div class="card-body">
                                  <form class="form" role="form" autocomplete="off" id="formPathological">
                                      <input type="hidden" name="pathological_customer_id" class="pathological_customer_id">
                                      <input type="hidden" name="pathological_create_by_id" class="pathological_create_by_id">
                                      <div class="form-group">
                                          <label for="uname1">Mã bệnh lý</label>
                                          <input type="text" class="form-control pathological_id" name="pathological_id" readonly placeholder="auto">
                                      </div>
                                      <div class="form-group">
                                          <label for="uname1">Tên khách hàng</label>
                                          <input type="text" class="form-control pathological_customer_name" name="pathologicalcustomer_name" disabled placeholder="auto">
                                      </div>
                                      <div class="form-group">
                                        <label for="uname1">Tên bệnh</label>
                                        <input type="text" class="form-control pathological_name" name="pathological_name"  placeholder="">
                                      </div>
                                      <div class="form-group">
                                          <label>Tình trạng bệnh</label>
                                          <textarea name="pathological_status" class="form-control pathological_status" cols="30" rows="10"></textarea>
                                      </div>
                                      <div class="form-group">
                                          <label>Thời gian tạo</label>
                                          <input name="pathological_date_create" type="text" class="form-control pathological_date_create" readonly value="{{date('Y-m-d H:i:s')}}">
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
                              <div class="card-body" style="min-height:100px">
                                  <ul id="history_pathological" class="list-unstyled">
                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
