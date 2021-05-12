@extends('layout.superadmin.default')
@section('title') Admin | Quản lý sản phẩm @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý cửa hàng'),
            'content' => [
                __('Quản lý cửa hàng') => route('superadmin.shop.create')
            ],
            'active' => [__('Quản lý cửa hàng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script src="{{url('js/source.js')}}"></script>
    <script>
        Common.datePicker('.date-picker');
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container">
            <form method="post" @if(isset($info)) action="{{route('superadmin.shop.update',$info->id)}}" @else action="{{route('superadmin.shop.store')}}" @endif>
                @csrf
                @if(isset($info))
                    {{ method_field("PUT") }}
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-right">
                                <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                                <a href="{{route('superadmin.shop.index')}}" class="btn btn-default"><i
                                        class="fa fa-backward mr-2"></i> Quay lại</a>
                            </div>
                            @include('elements.error_request')
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header card-header-default">
                                <h3 class="card-title">
                                    Thông tin cửa hàng
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Tên cửa hàng (<span class="text-danger">*</span>)</label>
                                    <input name="name" id="name" class="form-control" type="text" value="{!! isset($info->name) ? $info->name : old('name') !!}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Địa chỉ (<span class="text-danger">*</span>)</label>
                                    <input name="address" id="address" class="form-control" type="text" value="{!! isset($info->address) ? $info->address : old('address') !!}">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Số điện thoại (<span class="text-danger">*</span>)</label>
                                    <input name="phone" id="phone" class="form-control" type="text" value="{!! isset($info->phone) ? $info->phone : old('phone') !!}">
                                </div>
                                <div class="form-group">
                                    <label for="max_user">Số nhân viên tối đa (<span class="text-danger">*</span>)</label>
                                    <input name="max_user" id="max_user" class="form-control" type="text" value="{{ isset($info->max_user) ? old('max_user',$info->max_user) : old("max_user")  }}">
                                </div>
                                <div class="form-group">
                                    <label for="expired_date">Ngày hết hạn</label>
                                    <input name="expired_date" id="expired_date" class="form-control date-picker" type="text" value="{{ isset($info->expired_date) ? date("d/m/Y",strtotime(old('expired_date',$info->expired_date))) :old('expired_date') }}">
                                </div>
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <div>
                                        <label for="is_pause_off">
                                            <input id="is_pause_off" name="is_pause" type="radio" {{ isset($info->is_pause) && old('is_pause',$info->is_pause) == 1 ? "checked" : "" }} value="1">
                                            Tạm dừng
                                        </label>
                                        &emsp;
                                        <label for="is_pause_on">
                                            <input id="is_pause_on" name="is_pause" type="radio"  {{ isset($info->is_pause) && old('is_pause',$info->is_pause) == 0 ? "checked" : "" }}  value="0">
                                            Hoạt động
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header card-header-default">
                                <h3 class="card-title">
                                    Thông tin chủ cửa hàng
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="owner_username">Tên đăng nhập (<span class="text-danger">*</span>)</label>
                                    <input name="owner_username" id="owner_username" class="form-control" type="text" value="{!! isset($info->owner) ? $info->owner->account_id : old('owner_username') !!}" @if(isset($info->owner)) disabled @endif>
                                </div>
                                <div class="form-group">
                                    <label for="owner_password">Mật khẩu (<span class="text-danger">*</span>)</label>
                                    <input name="owner_password" id="owner_password" class="form-control" type="password">
                                </div>
                                <div class="form-group">
                                    <label for="owner_password_confirmation">Xác nhận mật khẩu (<span class="text-danger">*</span>)</label>
                                    <input name="owner_password_confirmation" id="owner_password_confirmation" class="form-control" type="password">
                                </div>
                                <div class="form-group">
                                    <label for="owner_name">Họ và tên (<span class="text-danger">*</span>)</label>
                                    <input name="owner_name" id="owner_name" class="form-control" type="text" value="{!! isset($info->owner)? $info->owner->name : old('owner_name') !!}">
                                </div>
                                <div class="form-group">
                                    <label for="owner_email">Email</label>
                                    <input name="owner_email" id="owner_email" class="form-control" type="text" value="{!! isset($info->owner) ? $info->owner->email : old('owner_email') !!}">
                                </div>
                                <div class="form-group">
                                    <label for="owner_phone">Số điện thoại</label>
                                    <input name="owner_phone" id="owner_phone" class="form-control" type="text" value="{!! isset($info->owner) ? $info->owner->phone : old('owner_phone') !!}">
                                </div>
                                <div class="form-group">
                                    <label for="owner_address">Địa chỉ</label>
                                    <input name="owner_address" id="owner_address" class="form-control" type="text" value="{!! isset($info->owner) ? $info->owner->address : old('owner_address') !!}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div><!-- /.container-fluid -->
    </section>
    {{-- @include('layout.flash_message') --}}
@stop
