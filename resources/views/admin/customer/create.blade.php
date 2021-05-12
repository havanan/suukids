@extends('layout.default')
@section('title') Admin | Thêm mới khách hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')

@php
    if(!isset($info)){
        $breadcrumb = [
            'title' => __('Thêm mới khách hàng'),
            'content' => [
            __('Thêm mới khách hàng') => route('admin.customer.create')
            ],
            'active' => [__('Thêm mới khách hàng')]
        ];
    }else{
        $breadcrumb = [
            'title' => __('Chỉnh sửa khách hàng'),
            'content' => [
            __('Chỉnh sửa khách hàng') => route('admin.customer.edit',$info)
            ],
            'active' => [__('Chỉnh sửa khách hàng')]
        ];
    }

@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <link href="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{url('js/source.js')}}"></script>
    <script src="{{url('js/common.js')}}"></script>
    <script>
        $("#image_url").change(function () {
            readImageUrl(this);
        });
        Common.datePicker('.date-picker');

        function selectContactId(contact_id, contact_name) {
            $("#contact_id").val(contact_id);
            $("#contact_name").val(contact_name);
        }
        @if($message = Session::get('error'))
        toastr.error('{{$message}}');
        @endif
        @if($message = Session::get('success'))
        toastr.success('{{$message}}');
        @endif
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container">
            @if(isset($info))
                <form method="post" action="{{route('admin.customer.update',$info->id)}}" enctype="multipart/form-data">
                    {{method_field('PUT')}}
                    @else
                        <form method="post" action="{{route('admin.customer.store')}}" enctype="multipart/form-data">
                            @endif
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header text-right">
                                            <button type="submit" class="btn btn-primary">Lưu lại</button>
                                            <a href="{{route('admin.customer.index')}}">
                                                <button type="button" class="btn btn-default">Quay lại</button>
                                            </a>
                                        </div>
                                        <!-- /.card-header -->
                                        @include('elements.error_request')
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 text-center">
                                                    <img src="@if(isset($info->avatar)){{url($info->avatar)}}
                                                    @else {{url('theme/admin-lte/dist/img/no_avatar.webp')}}
                                                    @endif" id="imagePreview" class="customer-avatar">
                                                    <div style="padding: 20px;">Thay ảnh: <input name="avatar"
                                                                                                 id="image_url"
                                                                                                 class="form-control"
                                                                                                 type="file"
                                                                                                 accept="image/gif,image/jpeg,image/jpg,image/png"
                                                                                                 value="">200x200 pixel
                                                        <br>(*.jpg,
                                                        *.jpeg, *.gif, *.png)
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header card-header-danger">
                                                            <h3 class="card-title">
                                                                <strong><i class="fa fa-clock"></i> Lịch sử</strong>
                                                            </h3>
                                                        </div>
                                                        <div class="card-body"
                                                             style="max-height: 254px;overflow-y: auto">
                                                            <ul class="list-group">

                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                        <tr>
                                                            <td width="120" align="right">Tên khách hàng(<span
                                                                    class="text-danger">*</span>):
                                                            </td>
                                                            <td width="150">
                                                                <input name="name" id="name" class="form-control"
                                                                       type="text"
                                                                       value="{{isset($info->name) ? $info->name : old('name')}}">
                                                            </td>
                                                            <td align="right">Nghề nghiệp:</td>
                                                            <td>
                                                                <input name="job" id="job" class="form-control"
                                                                       type="text"
                                                                       value="{{isset($info->job) ? $info->job : old('job')}}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" class="required">Cân nặng (kg):</td>
                                                            <td>
                                                                <input name="weight" id="weight" class="form-control"
                                                                       placeholder="00.00" type="number"
                                                                       value="{{isset($info->weight) ? $info->weight : old('weight')}}">
                                                            </td>
                                                            <td width="120" align="right">Chức vụ:</td>
                                                            <td nowrap="nowrap" width="150">
                                                                <input name="position" id="position"
                                                                       class="form-control"
                                                                       autocomplete="on" type="text"
                                                                       value="{{isset($info->position) ? $info->position : old('position')}}">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Ngày sinh:</td>
                                                            <td>
                                                                <input name="birthday" id="birthday"
                                                                       class="form-control date-picker"
                                                                       autocomplete="off"
                                                                       type="text"
                                                                       value="{{isset($info->birthday) ? $info->birthday : old('birthday')}}">
                                                            </td>
                                                            <td align="right">Nhóm phân loại(<span
                                                                    class="text-danger">*</span>):
                                                            </td>
                                                            <td>
                                                                <select name="customer_group_id" id="customer_group_id" required class="form-control">
                                                                    <option value="">Chọn</option>
                                                                    @if(isset($customer_groups) && count($customer_groups) > 0)
                                                                        @foreach($customer_groups as $item)
                                                                            <option value="{{$item->id}}"
                                                                                {{isset($info->customer_group_id) && $item->id == $info->customer_group_id || old('customer_group_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right">Email:</td>
                                                            <td>
                                                                <input name="email" id="email" class="form-control"
                                                                       type="text"
                                                                       value="{{isset($info->email) ? $info->email : old('email')}}">
                                                            </td>
                                                            <td align="right">Giới tính:</td>
                                                            <td>
                                                                <select class="form-control" name="sex">
                                                                    @if(count(USER_SEX) > 0)
                                                                        @foreach(USER_SEX as $key => $item)
                                                                            <option value="{{$key}}"
                                                                                {{isset($info->sex) && $key == $info->sex || old('sex') == $key ? 'selected' : ''}}>{{$item}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" class="required">
                                                                Di động(<span class="text-danger">*</span>):
                                                            </td>
                                                            <td>
                                                                <input name="phone" id="phone" class="form-control"
                                                                       placeholder="Số điện thoại phải là duy nhất"
                                                                       type="text"
                                                                       required
                                                                       value="{{isset($info->phone) ? $info->phone : old('phone')}}">
                                                            </td>
                                                            <td align="right">Tỉnh / thành phố:</td>
                                                            <td>
                                                                <select name="prefecture" id="prefecture"
                                                                        class="form-control">
                                                                    <option value="">Chọn</option>
                                                                    @if(isset($prefectures) && count($prefectures) > 0)
                                                                        @foreach($prefectures as $item)
                                                                            <option value="{{$item->id}}"
                                                                                {{isset($info->prefecture) && $item->id == $info->prefecture || old('prefecture') == $item->id ? 'selected' : ''}}>{{$item->_name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Địa chỉ:</td>
                                                            <td colspan="3">
                                                                <div>
                                                            <textarea name="address" id="address"
                                                                      class="form-control">{{isset($info->address)?$info->address:old('address')}}</textarea>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" valign="top">Người giới thiệu:</td>
                                                            <td>
                                                                <div class="input-group"
                                                                     onclick="goListCustomer('{{ route('admin.customer.index',['method' => 'select']) }}')">
                                                                    <input type="text" class="form-control"
                                                                           name="contact_name" id="contact_name"
                                                                           value="{{isset($info->contactUser->name) ? $info->contactUser->name : old('contact_name')}}"
                                                                           disabled>
                                                                    <input type="hidden" name="contact_id"
                                                                           id="contact_id"
                                                                           value="{{isset($info->contact_id) ? $info->contact_id : old('contact_id')}}">
                                                                    <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-search"></i>
                                                                </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td align="right" valign="top">
                                                                Nguồn khách:
                                                            </td>

                                                            <td>
                                                                <select name="source_id" id="source_id"
                                                                        class="form-control">
                                                                    <option value="0">Chọn</option>
                                                                    @if(isset($sources) && count($sources) > 0)
                                                                        @foreach($sources as $item)
                                                                            <option
                                                                                value="{{$item->id}}" {{isset($info->source_id) && $item->id == $info->source_id || old('source_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right">Nhân viên xác nhận:</td>
                                                            <td>
                                                                <select name="user_confirm_id" id="user_confirm_id"
                                                                        class="form-control">
                                                                    <option value="">Chọn</option>
                                                                    @if(isset($users) && count($users) > 0)
                                                                        @foreach($users as $item)
                                                                            <option
                                                                                value="{{$item->id}}" {{isset($info->user_confirm_id) && $item->id == $info->user_confirm_id || old('user_confirm_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td align="right">Tạo bởi:</td>
                                                            <td>
                                                                {{auth()->user()['name']}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" valign="top">Ghi chú chung:</td>
                                                            <td colspan="3">
                                                        <textarea name="note" id="note" class="form-control"
                                                                  rows="3">{{isset($info->note)?$info->note:old('note')}}</textarea>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right" valign="top" class="text-danger">Ghi chú
                                                                cảnh báo:
                                                            </td>
                                                            <td colspan="3">
                                                        <textarea name="note_alert" id="note_alert"
                                                                  class="form-control text-danger"
                                                                  rows="3">{{isset($info->note_alert)?$info->note_alert:old('note_alert')}}</textarea>
                                                            </td>
                                                        </tr>
                                                        </tbody>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header card-header-default">
                                            <h3 class="card-title">
                                                Tài khoản ngân hàng
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">Số tài khoản:
                                                    <input name="bank_account_number" id="bank_account_number"
                                                           class="form-control"
                                                           type="text"
                                                           value="{{isset($info->bank_account_number) ? $info->bank_account_number : old('bank_account_number')}}">
                                                </div>
                                                <div class="col-md-3">Tên tài khoản:
                                                    <input name="bank_account_name" id="bank_account_name"
                                                           class="form-control"
                                                           type="text"
                                                           value="{{isset($info->bank_account_name) ? $info->bank_account_name : old('bank_account_name')}}">
                                                </div>
                                                <div class="col-md-6">Tên ngân hàng:
                                                    <input name="bank_name" id="bank_name" class="form-control"
                                                           type="text"
                                                           value="{{isset($info->bank_name) ? $info->bank_name : old('bank_name')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

        </div><!-- /.container-fluid -->
    </section>
    <!-- Modal Call-->
    @includeIf('admin.customer.modal_call')
    <!-- End Modal Call-->
    <!-- Modal Note-->
    @includeIf('admin.customer.modal_note')
    <!-- End Modal Note-->
@stop
