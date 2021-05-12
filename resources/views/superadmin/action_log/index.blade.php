@extends('layout.superadmin.default')
@section('title') Admin | Quản lý tìm kiếm đơn hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý tìm kiếm đơn hàng'),
            'content' => [
                __('Quản lý tìm kiếm đơn hàng') => '#'
            ],
            'active' => [__('Quản lý tìm kiếm đơn hàng')]
        ];
    @endphp

    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}


@section('assets')
    <link href="{{ url('css/source.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('theme/admin-lte/plugins/bootstrap-table/css/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ url('theme/admin-lte/plugins/bootstrap-table/js/bootstrap-table.min.js') }}"></script>
    <script src="{{url('js/list.js')}}?{{filemtime('js/list.js')}}"></script>
    <script src="{{url('js/superadmin/action_log.js')}}?{{filemtime('js/superadmin/action_log.js')}}"></script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-md-12">
                                <div id="frm-enable" class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input name="user_name" id="user_name" class="form-control mr-2" placeholder="Nhập tên nhân viên" type="text">
                                        </div>
                                        <div class="col-md-2">
                                            <input name="query_content" id="query_content" class="form-control mr-2" placeholder="Nhập nội dung tìm kiếm" type="text">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="shop_id" id="shop_id" class="form-control select2">
                                                <option value="">Shop</option>
                                                @foreach($shops as $shop)
                                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select name="source" id="source" class="form-control">
                                                <option value="">Màn tìm kiếm</option>
                                                <option value="1">Tạo đơn mới</option>
                                                <option value="2">Quản lý đơn hàng</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select name="ip" id="ip" class="form-control">
                                                <option value="">Loại IP</option>
                                                <option value="1">Nội bộ</option>
                                                <option value="2">Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Ngày bắt đầu" readonly>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="end_date" name="end_date" placeholder="Ngày kết thúc" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-default" id="customSearch">
                                                <i class="fa fa-search"></i> Tìm kiếm
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="table-list"
                                        data-url="{{ url(route('superadmin.action_log.get_list')) }}"
                                        data-toggle="table"
                                        data-search="false"
                                        data-show-toggle="false"
                                        data-show-columns="true"
                                        data-sort-name="created_at"
                                        data-page-size="100"
                                        data-show-refresh="false"
                                        data-query-params="queryParamList"
                                        data-pagination="true"
                                        data-side-pagination="server"
                                        data-sort-order="desc"
                                        data-show-pagination-switch="false"
                                        data-buttons-toolbar="false"
                                        class="table-bordered table-list"
                                        data-row-style="rowStyle">
                                        <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true" data-width="50px" class="text-center">ID</th>
                                            <th data-field="user_name">Tên nhân viên</th>
                                            <th data-field="shop_name">Shop</th>
                                            <th data-field="content_query" data-formatter="formatterContentQuery">Nội dung tìm kiếm</th>
                                            <th data-field="url" data-formatter="formatterUrl">Màn</th>
                                            <th data-field="ip" data-formatter="formatterIp">Ip</th>
                                            <th data-field="created_at" data-sortable="true">Ngày tạo</th>
                                        </tr>
                                        </thead>
                                    </table>
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
