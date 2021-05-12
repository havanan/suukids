@extends('layout.default')
@section('title') Admin | Danh sách sản phẩm @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Danh sách sản phẩm'),
            'content' => [
                __('Danh sách sản phẩm') => route('admin.product.index')
            ],
            'active' => [__('Danh sách sản phẩm')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)

@stop
{{-- End Breadcrumb --}}
@section('header')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <link href="{{ url('theme/admin-lte/plugins/bootstrap-table/css/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css">
    <style>
       
    </style>
@endsection
@section('assets')
    <script>
        var urlDelete = 'product/delete';
    </script>
    <script src="{{ url('theme/admin-lte/plugins/bootstrap-table/js/bootstrap-table.min.js') }}"></script>
    <script src="{{url('js/list.js')}}?{{md5_file('js/list.js')}}"></script>
    <script src="{{url('js/product.js')}}?{{md5_file('js/product.js')}}"></script>
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
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="text-right">
                                        <button class="btn btn-default" data-toggle="modal" data-target="#modal-lg"><i class="fa fa-file-import mr-2"></i>Import Excel</button>
                                        <a href="{{route('admin.product.exportExcel')}}" class="btn btn-default" id="export"><span class="fa fa-cloud-download-alt" aria-hidden="true"></span> Export Excel </a>
                                        <a href="{{route('admin.product.create')}}" class="btn btn-success">
                                            <i class="fa fa-plus mr-2"></i> Thêm mới
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row" id="enable-search-form">
                                        <div class="col-md-4">
                                            <input type="text" name="keyword" id="keyword" class="form-control item-search-data" placeholder="Nhập tên, mã sản phẩm">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control item-search-data" name="bundle_id" id="bundle_id">
                                                <option value="">Chọn loại SP</option>
                                                @if(isset($bundles) && count($bundles) > 0)
                                                    @foreach($bundles as $key => $item)
                                                        <option value="{{$key}}">{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control item-search-data" name="unit_id" id="unit_id">
                                                <option value="">Chọn đơn vị</option>
                                                @if(isset($units) && count($units) > 0)
                                                    @foreach($units as $key => $item)
                                                        <option value="{{$key}}">{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control item-search-data" name="status" id="status">
                                                <option value="">Tình trạng</option>
                                                @if(count(PRODUCT_STATUS) > 0)
                                                    @foreach(PRODUCT_STATUS as $key => $item)
                                                        <option value="{{$key}}" @if($key == ACTIVE) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-default" id="customSearch"><i class="fa fa-search mr-2"></i>Tìm kiếm</button>
                                        </div>
                                    </div>
                                    <div class="row" id="disable-search-form">
                                        <div class="col-md-4">
                                            <input type="text" name="keyword" id="disable-keyword" class="form-control item-search-data" placeholder="Nhập tên, mã sản phẩm">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control item-search-data" name="bundle_id" id="disable-bundle_id">
                                                <option value="">Chọn loại SP</option>
                                                @if(isset($bundles) && count($bundles) > 0)
                                                    @foreach($bundles as $key => $item)
                                                        <option value="{{$key}}">{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control item-search-data" name="unit_id" id="disable-unit_id">
                                                <option value="">Chọn đơn vị</option>
                                                @if(isset($units) && count($units) > 0)
                                                    @foreach($units as $key => $item)
                                                        <option value="{{$key}}">{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control item-search-data" name="status" id="disable-status">
                                                <option value="">Tình trạng</option>
                                                @if(count(PRODUCT_STATUS) > 0)
                                                    @foreach(PRODUCT_STATUS as $key => $item)
                                                        <option value="{{$key}}" @if($key == INACTIVE) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-default" id="disable-customSearch"><i class="fa fa-search mr-2"></i>Tìm kiếm</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.popup import -->
                            @include('admin.product.import_form')
                        <!-- /.modal -->
                        <!-- /.card-header -->
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="custom-content-below-home-tab"
                                       data-toggle="pill"
                                       href="#custom-content-below-home"
                                       role="tab"
                                       aria-controls="custom-content-below-home"
                                       aria-selected="true">Dánh sách sản phẩm hàng hóa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-danger"
                                       id="custom-content-below-profile-tab"
                                       data-toggle="pill"
                                       href="#custom-content-below-profile"
                                       role="tab"
                                       aria-controls="custom-content-below-profile"
                                       aria-selected="false">Sản phẩm ngừng kinh doanh (Ẩn)</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="custom-content-below-tabContent">
                                <div class="tab-pane fade show active" id="custom-content-below-home" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <table id="table-list"
                                           data-url="{{ url(route('admin.product.getList')) }}"
                                           data-toggle="table"
                                           data-toolbar="#enable-search-form"
                                           data-search="false"
                                           data-show-toggle="false"
                                           data-show-columns="true"
                                           data-sort-name="id"
                                           data-page-size="10"
                                           data-show-refresh="false"
                                           data-query-params="queryParamProductList"
                                           data-pagination="true"
                                           data-side-pagination="server"
                                           data-sort-order="desc"
                                           data-show-pagination-switch="false"
                                           class="table-bordered table-list"
                                           data-row-style="rowStyle">
                                        <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true" data-width="50px" class="text-center">ID</th>
                                            <th data-field="product_image" data-formatter="avatarFormat">Ảnh</th>
                                            <th data-field="name" data-formatter="nameFormat">Tên sản phẩm</th>
                                            <th data-field="price" data-formatter="priceFormat">Giá bán</th>
                                            <th data-field="cost_price" data-formatter="priceFormat">Giá vốn</th>
                                            <th data-field="product_bundle">Loại</th>
                                            <th data-field="product_unit">Đơn vị</th>
                                            <th data-field="on_hand" data-formatter="priceFormat">Tồn kho</th>
                                            <th data-field="id" data-formatter="actionFormat" class="text-center"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="custom-content-below-profile" role="tabpanel" aria-labelledby="custom-content-below-profile-tab">
                                    <table id="table-disable-list"
                                           data-url="{{ url(route('admin.product.getList')) }}"
                                           data-toggle="table"
                                           data-toolbar="#disable-search-form"
                                           data-search="false"
                                           data-show-toggle="false"
                                           data-show-columns="true"
                                           data-sort-name="id"
                                           data-page-size="10"
                                           data-show-refresh="false"
                                           data-query-params="queryParamProductDisableList"
                                           data-pagination="true"
                                           data-side-pagination="server"
                                           data-sort-order="desc"
                                           data-show-pagination-switch="false"
                                           class="table-bordered table-list"
                                           data-row-style="rowStyle">
                                        <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true" data-width="50px" class="text-center">ID</th>
                                            <th data-field="product_image" data-formatter="avatarFormat">Ảnh</th>
                                            <th data-field="name" data-formatter="nameFormat">Tên sản phẩm</th>
                                            <th data-field="price" data-formatter="priceFormat">Giá bán</th>
                                            <th data-field="cost_price" data-formatter="priceFormat">Giá vốn</th>
                                            <th data-field="product_bundle">Loại</th>
                                            <th data-field="product_unit">Đơn vị</th>
                                            <th data-field="on_hand" data-formatter="priceFormat">Tồn kho</th>
                                            <th data-field="id" data-formatter="actionFormat" class="text-center"></th>
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
