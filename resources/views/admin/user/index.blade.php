@extends('layout.default')
@section('title') Admin | Quản lý tài khoản người dùng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý tài khoản người dùng'),
            'content' => [
                __('Quản lý tài khoản người dùng') => route('admin.user.index')
            ],
            'active' => [__('Quản lý tài khoản người dùng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script>
        $(document).ready(function(){
            $(".nav-tabs a").click(function(){
                $(this).tab('show');
            });
        });
        var urlDelete = '{{route('admin.user.destroy')}}';
    </script>
    <link href="{{ url('css/source.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('theme/admin-lte/plugins/bootstrap-table/css/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ url('theme/admin-lte/plugins/bootstrap-table/js/bootstrap-table.min.js') }}"></script>
    <script src="{{url('js/list.js')}}?{{md5_file('js/list.js')}}"></script>
    <script src="{{url('js/user.js')}}?{{md5_file('js/user.js')}}"></script>
    <script>
        // $('#table-list').bootstrapTable({
        //     onLoadSuccess:function countRow() {
        //         countTableRow('table-list','count-data')
        //     }
        // })
        // $('#table-disable-list').bootstrapTable({
        //     onLoadSuccess:function countRow() {
        //         countTableRow('table-disable-list','disable-count-data')
        //     }
        // })
    </script>
    @include('layout.flash_message')
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- /.modal -->
                            <div class="text-center" style="float: right">
                                <a href="{{route('admin.user.create')}}" class="btn  btn-custom-warning ">+ Thêm mới</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-md-12">
                                <div id="frm-enable" class="row">
                                    <div class="col-md-2">
                                        <input name="keyword" id="keyword" class="form-control mr-2"
                                               placeholder="Nhập tên tìm kiếm" type="text">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="account_group_id" id="account_group_id" class="form-control">
                                            <option value="">Tất cả các nhóm</option>
                                            @if(isset($user_groups) && count($user_groups) > 0)
                                                @foreach($user_groups as $item)
                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="permission_type" id="permission_type" class="form-control">
                                            <option value="">Tất cả các quyền</option>
                                            @foreach($permissions as $permission)
                                            <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-default" id="customSearch">
                                            <i class="fa fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <p id="count" class="count-data">Tổng: <span id="count-data">{{ $activeAccount }}</span> tài khoản</p>
                                    </div>
                                </div>
                            </div>
                            <div id="frm-disable" class="row">
                                <div class="col-md-3">
                                    <input name="keyword" id="disable-keyword" class="form-control "
                                           placeholder="Nhập tên tìm kiếm" type="text">
                                </div>
                                <div class="col-md-3">
                                    <select name="account_group_id" id="disable-account_group_id" class="form-control ">
                                        <option value="">Tất cả các nhóm</option>
                                        @if(isset($user_groups) && count($user_groups) > 0)
                                            @foreach($user_groups as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-default" id="disable-customSearch">
                                        <i class="fa fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                                <div class="col-md-4 text-right">
                                    <p id="disable-count">Tổng: <span id="disable-count-data">{{ $inActiveAccount }}</span> tài khoản</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-home-tab"
                                       data-toggle="tab"
                                       href="#nav-home"
                                       role="tab"
                                       aria-controls="nav-home"
                                       aria-selected="true">Tài khoản kích hoạt</a>
                                    <a class="nav-item nav-link" id="nav-profile-tab"
                                       data-toggle="tab"
                                       href="#nav-profile"
                                       role="tab"
                                       aria-controls="nav-profile"
                                       aria-selected="false">Tài khoản chưa kích hoạt</a>

                                </div>
                            </div>
                            <div class="row">
                                <div class="tab-content w-100" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <table id="table-list"
                                               data-url="{{ url(route('admin.user.getList')) }}"
                                               data-toggle="table"
                                               data-toolbar="#frm-enable"
                                               data-search="false"
                                               data-show-toggle="false"
                                               data-show-columns="true"
                                               data-sort-name="id"
                                               data-page-size="10"
                                               data-show-refresh="false"
                                               data-query-params="queryParamUserList"
                                               data-pagination="true"
                                               data-side-pagination="server"
                                               data-sort-order="desc"
                                               data-show-pagination-switch="false"
                                               class="table-bordered table-list"
                                               data-row-style="rowStyle">
                                            <thead>
                                            <tr>
                                                <th data-field="id" data-sortable="true" data-width="50px" class="text-center">ID</th>
                                                <th data-field="name" data-formatter="nameFormat">Tên tài khoản</th>
                                                <th data-field="account_id" data-formatter="infoAccFormat">Thông tin tài khoản</th>
                                                <th data-field="expried_day"data-formatter="expriedDayFormat">Thời hạn</th>
                                                <th data-field="created_at" data-formatter="userCreatedFormat">Ngày tạo</th>
                                                <th data-field="user_permission" data-formatter="roleFormat">Quyền</th>
                                                <th data-field="shop_manager_flag" data-formatter="actionFormat" class="text-center">QL SHOP</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <table id="table-disable-list"
                                               data-url="{{ url(route('admin.user.getList')) }}"
                                               data-toggle="table"
                                               data-toolbar="#frm-disable"
                                               data-search="false"
                                               data-show-toggle="false"
                                               data-show-columns="true"
                                               data-sort-name="id"
                                               data-page-size="10"
                                               data-show-refresh="false"
                                               data-query-params="queryParamUserDisableList"
                                               data-pagination="true"
                                               data-side-pagination="server"
                                               data-sort-order="desc"
                                               data-show-pagination-switch="false"
                                               class="table-bordered table-list"
                                               data-row-style="rowStyle">
                                            <thead>
                                            <tr>
                                                <th data-field="id" data-sortable="true" data-width="50px" class="text-center">ID</th>
                                                <th data-field="name" data-formatter="nameFormat">Tên tài khoản</th>
                                                <th data-field="account_id" data-formatter="infoAccFormat">Thông tin tài khoản</th>
                                                <th data-field="expried_day"data-formatter="expriedDayFormat">Thời hạn</th>
                                                <th data-field="created_at" data-formatter="userCreatedFormat">Ngày tạo</th>
                                                <th data-field="user_permission" data-formatter="roleFormat">Quyền</th>
                                                <th data-field="shop_manager_flag" data-formatter="actionFormat" class="text-center">QL SHOP</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop
