@extends('layout.default')
@section('title') Admin | Quản lý lịch sử đăng nhập @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý lịch sử đăng nhập'),
            'content' => [
                __('Quản lý lịch sử đăng nhập') => '#'
            ],
            'active' => [__('Quản lý lịch sử đăng nhập')]
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
    <script src="{{url('js/login_log.js')}}?{{filemtime('js/login_log.js')}}"></script>
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
                                    <div class="col-md-2">
                                        <input name="user_name" id="user_name" class="form-control mr-2" placeholder="Nhập tên nhân viên" type="text">
                                    </div>
                                    <div class="col-md-2">
                                        <input name="query_content" id="query_content" class="form-control mr-2" placeholder="Nhập từ khóa (IP)" type="text">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Ngày bắt đầu" readonly>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="end_date" name="end_date" placeholder="Ngày kết thúc" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-default" id="customSearch">
                                            <i class="fa fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="table-list"
                                        data-url="{{ url(route('admin.login_log.get_list')) }}"
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
                                            <th data-field="ip">IP</th>
                                            <th data-field="content_query" data-formatter="formatterContentQuery">Nội dung theo dõi</th>
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