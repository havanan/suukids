@extends('layout.default')
@section('title') Admin | Thêm mới người dùng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Thêm mới người dùng'),
            'content' => [
                __('Thêm mới người dùng') => route('admin.user.create')
            ],
            'active' => [__('Thêm mới người dùng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker.css')}}">
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker-regularfont.css')}}">
    <script src="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker.js')}}"></script>
    <script src="{{url('js/source.js')}}"></script>
    <script src="{{url('js/common.js')}}"></script>
    <script>
        $('#slb-color').simplecolorpicker({theme:'regularfont'});
        Common.datePicker('.date-picker');

        $(function() {
            $('#login_time_from').datetimepicker({
                format: 'HH:mm'
            });
            $('#login_time_to').datetimepicker({
                format: 'HH:mm'
            });
            $("#login_time_from").on("change.datetimepicker", function(e) {
                $('#login_time_to').datetimepicker('minDate', e.date);
            });
            $("#login_time_to").on("change.datetimepicker", function(e) {
                $('#login_time_from').datetimepicker('maxDate', e.date);
            });
        });
    </script>
@stop

@section('content')

    <section class="content">
        <div class="container">

            <form autocomplete="off"
                @if(isset($info)) action="{{route('admin.user.updateMember',$info['id'])}}"
                @else action="{{route('admin.user.save')}}"
                @endif method="post" id="frm-data">

                <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header text-right">
                                    <button class="btn btn-primary">Lưu lại</button>
                                    <a href="{{route('admin.user.index')}}" class="btn btn-default">Danh sách</a>
                                </div>
                                <!-- /.card-header -->
                                @include('elements.error_request')
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                <div class="row">
                    @csrf
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-orange">Tên đăng nhập<span class="text-danger ml-2">*</span></label>
                                            <input type="text" class="form-control" name="account_id"
                                                   @if(isset($info['account_id']))
                                                        value="{{$info['account_id']}}"
                                                        disabled
                                                   @else
                                                        value="{{old('account_id')}}"
                                                   @endif>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-orange">Mật khẩu<span class="text-danger ml-2">*</span></label>
                                            <input type="password" class="form-control" autocomplete="new-password" name="password" @if(!isset($info)) @endif>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-orange">Họ và tên <span class="text-danger ml-2">*</span></label>
                                            <input type="text" class="form-control" value="{!! isset($info['name']) ? $info['name']  :  old('name') !!}" name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" value="{{isset($info['email']) ? $info['email']  : old('email') }}" name="email" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Chọn màu cho tài khoản</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8 text-left">
                                        <div class="form-group">
                                            <select name="color" id="slb-color">
                                                @if(count(USER_COLOR) > 0)
                                                    @foreach(USER_COLOR as $key => $item)
                                                        <option value="{{$key}}"
                                                                @if(isset($info['color']) && $key == $info['color']) selected
                                                                @elseif( $key == old('color')) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ngày sinh </label>
                                            <input type="text" class="form-control date-picker"
                                                   value="{{isset($info['birthday']) && $info['birthday'] != null ?date('d/m/Y',strtotime($info['birthday'])):old('birthday')}}" name="birthday">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Giới tính </label>
                                            <select class="form-control" name="sex">
                                                @if(count(USER_SEX) > 0)
                                                    @foreach(USER_SEX as $key => $item)
                                                        <option value="{{$key}}" @if(isset($info['sex']) && $info['sex'] == $key) selected @elseif($key == old('sex')) selected @endif>{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Địa chỉ </label>
                                            <input type="text" class="form-control" value="{{isset($info['address']) ? $info['address']  : old('address')}}" name="address">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Số điện thoại </label>
                                            <input type="text" class="form-control" value="{{isset($info['phone']) ? $info['phone']  : old('phone')}}" name="phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ngày hết hạn </label>
                                            <input type="text" class="form-control date-picker"
                                                   value="{{isset($info['expried_day'] ) && $info['expried_day'] != null ?date('d/m/Y',strtotime($info['expried_day'])):old('expried_day')}}" name="expried_day">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Tỉnh </label>
                                            <select class="form-control" name="prefecture">
                                                <option value="">Chọn tỉnh</option>
                                                @if(isset($prefectures) && count($prefectures) > 0)
                                                    @foreach($prefectures as $item)
                                                        <option value="{{$item->id}}"
                                                                @if(isset($info['prefecture']) && $info['prefecture'] == $item->id  ) selected
                                                                @elseif($item->_code == old('prefecture')) selected
                                                            @endif>{{$item->_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Đầu số tổng đài </label>
                                            <input type="text" class="form-control" value="{{isset($info['extension']) ? $info['extension']  : old('extension')}}" name="extension">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Cho phép dùng cloudfone </label>
                                            <select class="form-control" name="active_cloudfone">
                                                <option value="1" @if(!empty($info['active_cloudfone'])) selected  @endif >Cho phép</option>
                                                <option value="0" @if(empty($info['active_cloudfone'])) selected  @endif>Không</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label >Số nội bộ cloudfone </label>
                                            <input type="text" class="form-control" value="{{isset($info['cloudfone_code']) ? $info['cloudfone_code']  : old('cloudfone_code')}}" name="cloudfone_code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header card-header-default">
                                                <h3 class="card-title">
                                                    Kích hoạt tài khoản
                                                </h3>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="checkboxPrimary1" name="status" value="{{ACTIVE}}"
                                                           @if(isset($info['status']) && $info['status'] == ACTIVE)
                                                                checked
                                                            @elseif(old('status') == ACTIVE)
                                                                checked
                                                            @endif>
                                                    <label for="checkboxPrimary1"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header card-header-default">
                                                <h3 class="card-title">
                                                    Nhóm tài khoản
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <select class="form-control" name="user_group_id">
                                                        <option value="">Chọn nhóm</option>
                                                        @if(isset($user_groups) && count($user_groups) > 0)
                                                            @foreach($user_groups as $item)
                                                                <option value="{{$item->id}}"
                                                                        @if(isset($info['user_group_id']) && $info['user_group_id'] == $item->id)
                                                                            selected
                                                                        @elseif($item->id == old('user_group_id'))
                                                                            selected
                                                                        @endif>{{$item->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header card-header-default">
                                                <h3 class="card-title">
                                                    Phân quyền
                                                </h3>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="row">
                                                    <div class="container-fluid mb-3 bdb-3">
                                                        <div class="row">
                                                            <div class="col-md-10">
                                                                <h5 class="text-orange">Quyền quản lý shop</h5>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="icheck-primary d-inline">
                                                                    <input type="checkbox" id="checkboxPrimary7"
                                                                           name="shop_manager_flag"
                                                                           value="1"
                                                                            @if(isset($info['shop_manager_flag']) && $info['shop_manager_flag'] == ACTIVE) checked
                                                                            @elseif(old('shop_manager_flag') == ACTIVE) checked
                                                                            @endif>
                                                                    <label for="checkboxPrimary7"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(isset($permissions) && count($permissions) > 0)
                                                        @foreach($permissions as $item)
                                                            <div class="col-md-6 text-left">
                                                                <div class="icheck-primary d-inline">
                                                                    <input type="checkbox" id="checkboxPermission{{$item->id}}" name="permission[]"
                                                                           @if(isset($user_permissions) && in_array($item->id,$user_permissions))
                                                                           checked
                                                                           @elseif(old('permission') != null && in_array($item->id,old('permission')))
                                                                           checked
                                                                           @endif
                                                                           value="{{$item->id}}">
                                                                    <label for="checkboxPermission{{$item->id}}">{{$item->name}}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header card-header-default">
                                                <h3 class="card-title">
                                                    Thời gian đăng nhập
                                                </h3>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="row">
                                                    <div class="row">
                                                        <div class='col-md-2'>
                                                            Từ
                                                        </div>
                                                        <div class='col-md-10'>
                                                            <div class="form-group">
                                                                <div class="input-group date" id="login_time_from" data-target-input="nearest">
                                                                    <input type="text" value="{{!empty($loginTimeFrom) ? $loginTimeFrom : old('login_time_from') }}" name="login_time_from" class="form-control datetimepicker-input" data-target="#login_time_from"/>
                                                                    <div class="input-group-append" data-target="#login_time_from" data-toggle="datetimepicker">
                                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class='col-md-2'>
                                                            Đến
                                                        </div>
                                                        <div class='col-md-10'>
                                                            <div class="form-group">
                                                                <div class="input-group date" id="login_time_to" data-target-input="nearest">
                                                                    <input type="text" name="login_time_to" value="{{!empty($loginTimeTo) ? $loginTimeTo : old('login_time_to') }}" class="form-control datetimepicker-input" data-target="#login_time_to"/>
                                                                    <div class="input-group-append" data-target="#login_time_to" data-toggle="datetimepicker">
                                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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
                </div>

            </form>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop
