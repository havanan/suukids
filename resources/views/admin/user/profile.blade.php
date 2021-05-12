@extends('layout.default')
@section('title') Admin | Thông tin tài khoản @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Thông tin tài khoản'),
'content' => [
__('Thông tin tài khoản') => route('admin.user.profile')
],
'active' => [__('Thông tin tài khoản')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<link rel="stylesheet" href="{{ url('css/source.css') }}">
<link rel="stylesheet" href="{{url('theme/admin-lte/plugins/daterangepicker/daterangepicker.css')}}">
<link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<script src="{{url('theme/admin-lte/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{url('js/source.js')}}"></script>
<script>
    $("#image_url").change(function() {
            readImageUrl(this);
        });
        datePicker();
</script>
    @include('layout.flash_message')
@stop

@section('content')
<section class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-right">
                        <button class="btn btn-primary" onclick="submitForm()"><i class="fa fa-save mr-2"></i>Lưu
                            lại</button>
                        <button class="btn btn-default" data-toggle="modal" data-target="#modal-default">
                            <i class="fa fa-keyboard mr-2"></i> Đổi mật khẩu
                        </button>
                    </div>
                    <!-- /.card-header -->
                    @include('elements.error_request')
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>

        <div class="modal fade" id="modal-default">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Đổi Mật Khẩu</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{route('admin.user.updatePass')}}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Mật khẩu đang sử dụng <span class="text-danger ml-2">*</span></label>
                                <input type="password" name="old_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Mật khẩu mới <span class="text-danger ml-2">*</span></label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nhập lại mật khẩu mới <span class="text-danger ml-2">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="text-left">
                                <span class="text-orange small">Mật khẩu phải an toàn (<strong>Số + chữ + ký
                                        tự</strong>)<br>mới có thể cập nhật được</span>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save mr-2"></i>Cập
                                nhật</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <form action="{{route('admin.user.update',auth()->user()->id)}}" method="post" class="row" id="frm-data"
            enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="box box-widget widget-user">

                                    <div class="widget-user-header bg-aqua-active" @if(auth()->user()['color'])
                                        style="background-color: '{{auth()->user()['color']}}'" @endif>
                                        <h3 class="widget-user-username text-white">{!! auth()->user()['name'] !!}</h3>
                                        <h5 class="widget-user-desc text-white">{{auth()->user()['account_id']}} (ID:
                                            {{auth()->user()['id']}})</h5>
                                    </div>
                                    <div class="widget-user-image">
                                        <img class="img-circle profile-preview" src="@if(auth()->user()['avatar']){{url(auth()->user()['avatar'])}}
                                            @else {{url('theme/admin-lte/dist/img/no_avatar.webp')}} @endif"
                                            id="imagePreview" alt="{{auth()->user()['name']}}">
                                    </div>
                                    <div class="box-footer">
                                        <div class="row hidden">
                                            <div class="col-sm-4 border-right">
                                                <div class="description-block">
                                                    <h5 class="description-header">{{$data['waiting']}}</h5>
                                                    <span class="description-text">Đơn chưa xác nhận</span>
                                                </div>

                                            </div>

                                            <div class="col-sm-4 border-right">
                                                <div class="description-block">
                                                    <h5 class="description-header">{{$data['done']}}</h5>
                                                    <br><span class="description-text">Đơn chốt</span>
                                                </div>

                                            </div>

                                            <div class="col-sm-4">
                                                <div class="description-block">
                                                    <h5 class="description-header">{{$data['success']}}</h5>
                                                    <span class="description-text">Đơn thành công</span>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header card-header-default">
                                    </div>
                                    <div class="card-body">
                                        <div class="small">
                                            Thay ảnh: <input name="avatar" id="image_url" class="form-control"
                                                accept="image/gif,image/jpeg,image/jpg,image/png" type="file">
                                            <span class="text-danger">Ảnh < 8M (*.jpg, *.jpeg, *.gif, *.png)</span>
                                                    </div> </div> </div> </div> <div class="col-md-8">
                                                    <div class="container">
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Họ và tên<span
                                                                    class="text-danger ml-2">*</span></label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="name"
                                                                    value="{!! auth()->user()['name'] !!}" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Email<span
                                                                    class="text-danger ml-2">*</span></label>
                                                            <div class="col-md-9">
                                                                <input type="email" class="form-control" name="email"
                                                                    value="{{auth()->user()['email']}}" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Điện thoại<span
                                                                    class="text-danger ml-2">*</span></label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="phone"
                                                                    value="{{auth()->user()['phone']}}" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Địa chỉ</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="address"
                                                                    value="{{auth()->user()['address']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Khu vực</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" name="prefecture">
                                                                    <option value="">Chọn tỉnh</option>
                                                                    @if(isset($prefectures) && count($prefectures) > 0)
                                                                    @foreach($prefectures as $item)
                                                                    <option value="{{$item->id}}" @if($item->id ==
                                                                        auth()->user()['prefecture']) selected
                                                                        @endif>{{$item->_name}}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Skype</label>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="skype"
                                                                    value="{{auth()->user()['skype']}}">
                                                            </div>
                                                        </div>
                                                        {{--chọn đối tác vận đơn: viettel post: vtp, EMS: ems--}}
                                                        @if (auth()->user()->isAdmin())
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label">Đối tác vận chuyển</label>
                                                            <div class="col-md-9">
                                                                <select name="shipping_partner" class="form-control">
                                                                    <option value="vtp" {{ auth()->user()['shipping_partner'] == 'vtp' ? 'selected' : '' }}>Viettel Post</option>
                                                                    <option value="ems" {{ auth()->user()['shipping_partner'] == 'ems' ? 'selected' : '' }}>EMS</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                    <label>Giới tính </label>
                                                                    <select class="form-control" name="sex">
                                                                        @if(count(USER_SEX) > 0)
                                                                        @foreach(USER_SEX as $key => $item)
                                                                        <option value="{{$key}}" @if($key==auth()->
                                                                            user()['sex']) selected @endif>{{$item}}
                                                                        </option>
                                                                        @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Ngày sinh </label>
                                                                    <input type="text" name="birthday"
                                                                        class="date-picker form-control"
                                                                        value="{{auth()->user()['birthday'] != null ? date('d-m-Y',strtotime(auth()->user()['birthday'])) : ''}}" />

                                                                    {{--                                                    <input type="text" class="form-control date-picker" name="birthday" value="{{auth()->user()['birthday']}}">--}}
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Số CMTND </label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{auth()->user()['cmtnd']}}"
                                                                        name="cmtnd">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->

        </form>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@stop
