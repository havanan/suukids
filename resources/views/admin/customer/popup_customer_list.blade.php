<script>
    Common.datePicker('.date-picker');
</script>

<div class="card">
    <div class="card-header" id="frm-search">
        <!-- /.modal -->
{{--        <div class="row">--}}
{{--            <div class="col-md-12 mb-3">--}}
{{--                <div class="text-right">--}}
{{--                    <button data-date="{{date('d/m/Y')}}" class="btn btn-default" id="btn-birthday-today"><i class="fa fa-calendar-alt mr-2"></i>SN hôm nay</button>--}}
{{--                    <button class="btn btn-default" id="btn-birthday-week"><i class="fa fa-calendar-alt mr-2"></i>SN tuần này</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control" id="type" name="type" style="border: 1px dashed red;">
                            <option value="">Phân loại</option>
                            <option value="">Mới</option>
                            <option value="">Tiếp tục mua</option>
                            <option value="">Hẹn gọi lại</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="created_from" class="form-control date-picker" name="created_from" placeholder="Từ ngày"
                        >
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="created_to" class="form-control date-picker" name="created_to" placeholder="Đến ngày"
                        >
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="name" class="form-control" name="name" placeholder="Họ tên">
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="phone" class="form-control" name="phone" placeholder="Số điện thoại">
                    </div>
                    <div class="col-md-2" id="customer_group_id">
                        <select class="form-control" name="customer_group_id">
                            <option value="">Xem theo nhóm</option>
                            <option value="">Khách mới</option>
                            <option value="">Khách quen</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-right">
                <button class="btn btn-default" id="customSearch"><i class="fa fa-search mr-2"></i>Tìm kiếm</button>
                <button class="btn btn-default"><i class="fa fa-refresh mr-2"></i>Tìm lại</button>
            </div>
        </div>

    </div>
    <div class="card-body">
        <table id="table-list"
               data-url="{{ url(route('admin.customer.getList')) }}"
               data-toggle="table"
               data-toolbar="#frm-search"
               data-search="false"
               data-show-toggle="false"
               data-show-columns="true"
               data-sort-name="id"
               data-page-size="10"
               data-show-refresh="false"
               data-query-params="queryParamCustomerFind"
               data-pagination="true"
               data-side-pagination="server"
               data-sort-order="desc"
               data-show-pagination-switch="false"
               class="table-bordered table-list"
               data-row-style="rowStyle">
            <thead>
            <tr>
                <th data-field="id" data-sortable="true" data-width="50px" class="text-center">STT</th>
                <th data-field="name" data-formatter="customerFormat">Khách hàng</th>
                <th data-field="product_bundle" data-formatter="orderFormat">ĐH mới nhất</th>
                <th data-field="customer_groups_name" data-formatter="groupFormat">Phân nhóm</th>
                <th data-field="user_confirm_name" data-formatter="userConfirmFormat">Phụ trách</th>
                <th data-field="created_by_name" data-formatter="userCreatedFormat">Người tạo</th>
{{--                <th data-field="id" data-formatter="bntViewFormat" class="text-center">Xem</th>--}}
                <th data-field="id" data-formatter="bntNoteFormat">Note</th>
                <th data-field="id" data-formatter="bntCallFormat">C.gọi</th>
{{--                <th data-field="id" data-formatter="bntEditFormat" class="text-center">Sửa</th>--}}
            </tr>
            </thead>
        </table>
    </div>
</div>

<link href="{{ url('theme/admin-lte/plugins/bootstrap-table/css/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css">
<script src="{{ url('theme/admin-lte/plugins/bootstrap-table/js/bootstrap-table.min.js') }}"></script>
<script src="{{url('js/customer/index.js')}}?{{md5_file('js/customer/index.js')}}"></script>
<script src="{{url('js/list.js')}}"></script>
<script src="{{url('js/customer/find.js')}}?{{md5_file('js/customer/find.js')}}"></script>
