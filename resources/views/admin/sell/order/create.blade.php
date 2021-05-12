@extends('layout.default')
@section('title') Admin | Thêm mới đơn hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Thêm mới đơn hàng'),
'content' => [
__('Thêm mới đơn hàng') => route('admin.sell.order.create')
],
'active' => [__('Thêm mới đơn hàng')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('header')
<style>
    .step-ward-arrow {
        font-size: 14px;
        text-align: center;
        color: #666;
        cursor: default;
        margin: 0 3px;
        padding: 8px 10px 8px 30px;
        width: 100%;
        position: relative;
        background-color: #f1f1f1;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        transition: background-color .2s ease;
        left: -5px
    }

    .step-ward-arrow:after,
    .step-ward-arrow:before {
        content: " ";
        position: absolute;
        top: 0;
        right: -17px;
        width: 0;
        height: 0;
        border-top: 19px solid transparent;
        border-bottom: 17px solid transparent;
        border-left: 17px solid #f1f1f1;
        z-index: 2;
        transition: border-color .2s ease
    }

    .step-ward-arrow.active {
        background-color: #00c0ff;
        color: #fff
    }

    .step-ward-arrow.active:after,
    .step-ward-arrow.active:before {
        border-left: 17px solid #00c0ff
    }

    .step-ward-arrow:before,
    .step-ward-arrow.active:before {
        right: auto;
        left: 0;
        border-left: 17px solid #fff;
        z-index: 0
    }

    .step-ward-box {
        border-right: 1px dotted #ccc;
        margin-top: 10px;
        padding: 5px;
        height: 100%;
    }
</style>
@endsection

@section('assets')
@include('layout.flash_message')
<link rel="stylesheet" href="{{ url('js/plugins/jquery.datetimepicker.min.css') }}">
<script src="{{ url("js/function.js") }}?v={{ filemtime('js/function.js') }}"></script>
<script src="{{ url('js/plugins/jquery.datetimepicker.full.min.js') }}"></script>
<script>
    let urlSubmit = "{{ route('admin.sell.order.store') }}"
    let urlSearchOrderByPhone = "{{ route('admin.sell.order.search-by-phone') }}"
</script>
@include('admin.sell.order.script')
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <form action="{{route('admin.sell.order.store')}}" method="post" id="create-form">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-right">
                            <button type="button" class="btn btn-primary" id="submit_btn">Lưu lại</button>
                            <a href="{{route('admin.sell.order.index')}}" class="btn btn-default">Danh sách</a>
                        </div>
                        <!-- /.card-header -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                @csrf
                <input type="hidden" name="customer[id]" value="{{ request()->input('customer_id') }}">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Thông tin khách hàng</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                                class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <div>Mã đơn hàng</div>
                                                <small class="badge badge-danger">Tự động</small>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label for="shipping_code">Mã vận chuyển</label>
                                                <input type="text" name="shipping_code" class="form-control"
                                                    id="shipping_code" placeholder="Nhập mã vận chuyển">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Customer Name  -->
                                    <div class="form-group row">
                                        <label for="customer_name" class="col-md-4 col-form-label">Tên khách
                                            hàng</label>
                                        <div class="col-md-8">
                                            <input type="text" name="customer[name]" class="form-control" id="customer_name"
                                                placeholder="Tên khách hàng">
                                        </div>
                                    </div>
                                    <!-- End Customer Name -->
                                    <!-- Customer Phone  -->
                                    <div class="{{ request()->input('customer_id') > 0 ? 'd-none' : '' }} form-group row">
                                        <label class="col-md-4 col-form-label">Số điện thoại</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="customer_primary_phone" name="customer[phone]"
                                                   placeholder="Sđt chính" maxlength="11" onchange="suggestCustomerByPhone()">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="customer[phone2]"
                                                placeholder="Sđt phụ">
                                        </div>
                                    </div>
                                    <!-- End Customer Phone  -->
                                    <!-- Customer Email  -->
                                    <div class="form-group row">
                                        <label for="customer_email" class="col-md-4 col-form-label">Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="customer[email]" id="customer_email"
                                                placeholder="Email">
                                        </div>

                                    </div>
                                    {{--<div class="form-group row">--}}
                                        {{--<label class="col-md-4 col-form-label">Đầu bệnh nguồn</label>--}}
                                        {{--<div class="col-md-8">--}}
                                            {{--<select id="customer_bundle" name="customer[bundle_id]" class="form-control">--}}
                                            {{--<option value="">- Chọn đầu bệnh nguồn - </option>--}}
                                            {{--@foreach($bundle_arr as $bundle)--}}
                                            {{--<option value="{{ $bundle->id }}">{{ $bundle->name }}</option>--}}
                                            {{--@endforeach--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    <!-- End Customer Email  -->
                                    <!-- Customer Address  -->
                                    <div class="form-group row">
                                        <label for="customer_address" class="col-md-4 col-form-label">Địa chỉ</label>
                                        <div class="col-md-8">
                                            <textarea class="form-control" name="customer[address]" id="customer_address" rows="2"
                                                placeholder="Địa chỉ"></textarea>
                                        </div>
                                    </div>
                                    <!-- End Customer Address  -->

                                    <!-- Customer Province -->
                                    <div class="form-group row">
                                        <label for="customer_address" class="col-md-4 col-form-label">Tỉnh/Thành
                                            phố</label>
                                        <div class="col-md-8">
                                            <div class="input-group input-group-md">
                                                <input type="text" name="customer_province" id="customer_province"
                                                    class="form-control">
                                                <span class="input-group-append">
                                                    <button type="button" class="btn btn-info btn-flat"
                                                        data-toggle="modal" data-target="#modal-province">Chọn tỉnh
                                                        thành</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Customer Province -->

                                    <!-- Note -->
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="note1">Ghi chú chung</label>
                                                <textarea type="text" rows="2" name="note1" class="form-control"
                                                     id="note1" placeholder=""></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="note2">Ghi chú khác</label>
                                                <textarea type="text" rows="2" name="note2" class="form-control"
                                                          id="note2" placeholder=""></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipping_note">Ghi chú giao hàng</label>
                                                <textarea type="text" rows="2" name="shipping_note" class="form-control"
                                                    id="shipping_note" placeholder=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Thông tin đơn hàng</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                                class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!--  -->
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <div class="custom-control custom-checkbox" style="width:100%">
                                                <input class="custom-control-input" name="is_top_priority"
                                                    type="checkbox" id="is_top_priority">
                                                <label for="is_top_priority" class="custom-control-label"
                                                    style="font-weight:300">Ưu tiên</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" name="is_send_sms" id="is_send_sms"
                                                    type="checkbox">
                                                <label for="is_send_sms" class="custom-control-label"
                                                    style="font-weight:300">Đã SMS</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" name="is_inner_city"
                                                    id="is_inner_city" type="checkbox">
                                                <label for="is_inner_city" class="custom-control-label"
                                                    style="font-weight:300">Nội thành</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="shipping_service_id" class="col-md-5 col-form-label">Trạng
                                            thái</label>
                                        <div class="col-md-7">
                                            <div id="stt-btn-custom" class="w-100 form-control">
                                                <span id="stt-color-custom"
                                                    style="display: inline-block;width:10px;height:10px ;margin-right: 5px;background-color:#76ec39"></span>
                                                <span id="stt-name-custom" style="font-size:13px">Chưa xác nhận</span>
                                            </div>
                                            <button class="btn btn-warning"
                                                style="font-size:13px; height:30px; padding:0px;width: 100%;margin-top:2px;border-radius: 10px;border: 2px solid #ffffff;box-shadow: 1px 2px 3px #999;"
                                                type="button" id="btn-change-status" data-toggle="modal"
                                                data-target="#modal-status">
                                                Đổi trạng thái
                                            </button>
                                        </div>
                                    </div>
                                    <!-- shipping_service_id -->
                                    <div class="form-group row">
                                        <label for="shipping_service_id" class="col-md-5 col-form-label">Giao
                                            hàng</label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="shipping_service_id">
                                                <option value="0">--Chọn--</option>
                                                @foreach($deliveryMethods as $key => $method)
                                                <option value="{{$method->id}}">{{$method->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /shipping_service_id -->
                                    <!-- bundle_id  -->
                                    <div class="form-group row">
                                        <label for="bundle_id" class="col-md-5 col-form-label">Loại S/p (của đơn này)</label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="bundle_id" id="bundle_id">
                                                <option value="0">--Chọn--</option>
                                                @foreach($productBundles as $key => $bundle)
                                                <option value="{{$bundle->id}}">{{$bundle->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /bundle_id -->
                                    <div class="form-group row">
                                        <label for="source_id" class="col-md-5">Nguồn</label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="source_id">
                                                @foreach($orderSources as $key => $source)
                                                <option value="{{$source->id}}" {{ $source->default_select ? "selected" : "" }}>{{$source->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="type" class="col-md-5">Loại đơn hàng</label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="type">
                                                {{-- <option value="0">--Chọn--</option> --}}
                                                @foreach($order_types as $key => $type)
                                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- user_created -->
                                    <div class="form-group row">
                                        <label for="user_created" class="col-md-5 col-form-label">Người tạo đơn</label>
                                        <div class="col-md-7">
                                            @if(getCurrentUser()->isAdmin ())
                                                <select class="form-control" name="user_created">
                                                    <option value="0">--Chọn--</option>
                                                    @foreach($users as $key => $user)
                                                        @if(getCurrentUser()->isAdmin ())
                                                            <option value="{{$user->id}}" @if(getCurrentUser()->id == $user->id) selected @endif>{!! $user->name !!}</option>
                                                        @else
                                                            <option value="{{$user->id}}">{!! $user->name !!}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @else
                                                <p><strong>
                                                        @foreach($users as $key => $user)
                                                            @if(getCurrentUser()->id == $user->id)
                                                                {!! getCurrentUser()->name !!}
                                                            @endif
                                                        @endforeach
                                                    </strong></p>
                                                <input type="hidden" name="user_created" value="{{getCurrentUser()->id}}">
                                            @endif
                                        </div>
                                    </div>
                                    <!-- /user_created -->
                                    <!-- upsale_from_user_id -->

                                    <div class="form-group row">
                                        <label for="upsale_from_user_id" class="col-md-5 col-form-label">Nguồn Up Sale</label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="upsale_from_user_id">
                                                <option value="">hotline</option>
                                                @if(!getCurrentUser()->hasPermission('disable_edit_upsale_flag') || getCurrentUser()->isAdmin())
                                                    @foreach($marketings as $key => $user)
                                                    <option value="{{$user->id}}" @if($user->id == getCurrentUser()->id) selected @endif>{{$user->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /upsale_from_user_id -->
                                    @if(getCurrentUser()->hasPermission('assign_order_for_sale'))
                                    <!-- assigned_user_id -->
                                    <div class="form-group row">
                                        <label for="assigned_user_id" class="col-md-5 col-form-label">Chia đơn
                                            cho</label>
                                        <div class="col-md-7">
                                            <select class="form-control" name="assigned_user_id">
                                                <option value="">--Chọn--</option>
                                                @foreach($sales as $key => $user)
                                                <option value="{{$user->id}}" @if($user->id == getCurrentUser()->id) selected @endif>{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    <!-- /assigned_user_id -->
                                    <!-- cancel_note -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="cancel_note">Lý do hủy / Xem xét lại</label>
                                                <textarea type="text" rows="2" name="cancel_note" class="form-control"
                                                    id="cancel_note" placeholder=""></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /cancel_note -->
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Sản phẩm / Hàng hóa</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                                class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" id="products">
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-info" id="add-product-btn">Thêm</button>
                                </div>
                                <!-- /.card-footer-->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Chi phí</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- price -->
                            <div class="form-group row">
                                <div class="col-md-5">Thành tiền</div>
                                <div class="col-md-7">
                                    <input type="text" disabled class="form-control" id="products-price" name="price" placeholder="">
                                </div>
                            </div>
                            <!-- /price -->
                            <!-- discount_price -->
                            <div class="form-group row">
                                <div class="col-md-5">Giảm giá</div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="discount_price" onchange="updateTotalPrice()" id="discount_price">
                                </div>
                            </div>
                            <!-- /discount_price -->
                            <!-- shipping_price -->
                            <div class="form-group row">
                                <div class="col-md-5">Phí vận chuyển</div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="shipping_price" onchange="updateTotalPrice()" id="shipping_price">
                                </div>
                            </div>
                            <!-- /shipping_price -->
                            <!-- other_price -->
                            <div class="form-group row">
                                <div class="col-md-5">Phụ thu</div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="other_price" onchange="updateTotalPrice()" id="other_price">
                                </div>
                            </div>
                            <!-- /other_price -->
                            <!-- total_price -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label">Thành tiền</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="total_price" id="total_price" disabled>
                                </div>
                            </div>
                            <!-- /total_price -->
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div id="appointment-list"></div>
                            <button id="add-appointment-btn" type="button" class="btn btn-success">Thêm mới lịch hẹn</button>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.sell.order.province')
            @include('admin.sell.order.status')
            @include('admin.sell.order.suggest_customer')
        </form>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@stop
