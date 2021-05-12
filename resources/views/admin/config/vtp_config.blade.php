@php
use Illuminate\Support\Arr;
@endphp
@extends('layout.default')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#account" data-toggle="tab">Tài khoản</a></li>
                            <li class="nav-item"><a class="nav-link" href="#province" data-toggle="tab">Tỉnh thành</a></li>
                            <li class="nav-item"><a class="nav-link" href="#buucuc" data-toggle="tab">Bưu cục</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="account">
                                <form class="form-horizontal" id="frmAccount">
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Username</label>
                                        <div class="col-sm-10">
                                            <input name="username" class="form-control" placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail" class="col-sm-2 col-form-label">Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" name="password" class="form-control" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">User ID</label>
                                        <div class="col-sm-10">
                                            <input readonly class="form-control" id="userId" value="{{ Arr::get($settings,'vtp.userId') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail" class="col-sm-2 col-form-label">Token code</label>
                                        <div class="col-sm-10">
                                            <textarea readonly rows="3" id="token" class="form-control">{{ Arr::get($settings,'vtp.token') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Lưu</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="province">
                                <button class="mb-2 btn btn-sm btn-success" onclick="getProvince()"><i class="fa fa-download"></i> Lấy danh sách tỉnh thành</button>
                                <button class="mb-2 btn btn-sm btn-success" onclick="getDistrict()"><i class="fa fa-download"></i> Lấy danh sách quận huyện</button>
                                @if (request()->input('districtId'))
                                <button class="mb-2 btn btn-sm btn-success" onclick="getWard({{ request()->input('districtId') }})"><i class="fa fa-download"></i> Lấy danh sách phường xã</button>
                                @endif
                                @if (request()->input('provinceId'))
                                    <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="/admin/config/vtp?tab=province">Tỉnh thành</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:;">{{ $selected_province->_name }}</a></li>
                                </ol>
                                @endif
                                @if (request()->input('districtId'))
                                    <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="/admin/config/vtp?tab=province">Tỉnh thành</a></li>
                                    <li class="breadcrumb-item"><a href="/admin/config/vtp?tab=district&provinceId={{ $selected_province->id }}">{{ $selected_province->_name }}</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:;">{{ $selected_district->_name }}</a></li>
                                </ol>
                                @endif
                                <table class="table table-striped projects">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">
                                                #
                                            </th>
                                            <th style="width: 20%">
                                                Tên
                                            </th>
                                            <th style="width: 30%">
                                                Mã hệ thống
                                            </th>
                                            <th class="text-center">
                                                Mã VTP
                                            </th>
                                            <th class="text-center">
                                                ID VTP
                                            </th>
                                            <th style="width: 20%">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (request()->input('tab') === 'district')
                                            @foreach($district as $item)
                                                <tr data-name="{{ $item->_name }}">
                                                    <td>
                                                        {{ $loop->index+1 }}
                                                    </td>
                                                    <td>
                                                        {{ $item->_name }}
                                                    </td>
                                                    <td>
                                                        {{ $item->_code }}
                                                    </td>
                                                    <td>
                                                        <input readonly value="{{ $item->vtp_code }}" class="js-code form-control">
                                                    </td>
                                                    <td>
                                                        <input readonly value="{{ $item->vtp_id }}" class="js-id form-control">
                                                    </td>
                                                    <td class="project-actions text-right">
                                                        <a class="btn btn-primary btn-xs" href="/admin/config/vtp?tab=ward&districtId={{ $item->id }}">
                                                            <i class="fas fa-folder">
                                                            </i>
                                                            Xem DS phường, xã ({{ $item->wards->count() }})
                                                        </a>
                                                        <a class="btn btn-info btn-xs" href="javascript:;">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                        <a class="btn btn-danger btn-xs" href="javascript:;">
                                                            <i class="fas fa-trash">
                                                            </i>
                                                            Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif (request()->input('tab') === 'ward')
                                            @foreach($ward as $item)
                                                <tr data-name="{{ $item->_name }}">
                                                    <td>
                                                        {{ $loop->index+1 }}
                                                    </td>
                                                    <td>
                                                        {{ $item->_name }}
                                                    </td>
                                                    <td>
                                                        {{ $item->_code }}
                                                    </td>
                                                    <td>
                                                        <input readonly value="{{ $item->vtp_code }}" class="js-code form-control">
                                                    </td>
                                                    <td>
                                                        <input readonly value="{{ $item->vtp_id }}" class="js-id form-control">
                                                    </td>
                                                    <td class="project-actions text-right">
                                                        <a class="btn btn-info btn-xs" href="javascript:;">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                        <a class="btn btn-danger btn-xs" href="javascript:;">
                                                            <i class="fas fa-trash">
                                                            </i>
                                                            Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach($province as $item)
                                                <tr data-name="{{ $item->_name }}">
                                                    <td>
                                                        {{ $loop->index+1 }}
                                                    </td>
                                                    <td>
                                                        {{ $item->_name }}
                                                    </td>
                                                    <td>
                                                        {{ $item->_code }}
                                                    </td>
                                                    <td>
                                                        <input readonly value="{{ $item->vtp_code }}" class="js-code form-control">
                                                    </td>
                                                    <td>
                                                        <input readonly value="{{ $item->vtp_id }}" class="js-id form-control">
                                                    </td>
                                                    <td class="project-actions text-right">
                                                        <a class="btn btn-primary btn-xs" href="/admin/config/vtp?tab=district&provinceId={{ $item->id }}">
                                                            <i class="fas fa-folder">
                                                            </i>
                                                            Xem DS quận huyện ({{ $item->districts->count() }})
                                                        </a>
                                                        <a class="btn btn-info btn-xs" href="javascript:;">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                        <a class="btn btn-danger btn-xs" href="javascript:;">
                                                            <i class="fas fa-trash">
                                                            </i>
                                                            Delete
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="buucuc">
                                <button class="mb-2 btn btn-sm btn-success" onclick="getBuuCuc()"><i class="fa fa-download"></i> Lấy danh sách bưu cục</button>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('assets')
<script>
    function getProvince(){
        $.ajax({
            url:'/admin/vtp/list-province',dataType:'json',
            success:function(resp){
                if (resp.success) {
                    toastr['success'](resp.msg);
                    for(var i=0;i<resp.data.length;i++){
                        if ($('tr[data-name="'+resp.data[i]['PROVINCE_NAME']+'"]').length) {
                            $('tr[data-name="'+resp.data[i]['PROVINCE_NAME']+'"]').find('.js-code').val(resp.data[i]['PROVINCE_CODE']);
                            $('tr[data-name="'+resp.data[i]['PROVINCE_NAME']+'"]').find('.js-id').val(resp.data[i]['PROVINCE_ID']);
                        }
                    }
                } else {
                    toastr['error'](resp.msg);
                }
            }
        })
    }
    function getDistrict(){
        $.ajax({
            url:'/admin/vtp/list-district',dataType:'json',
            success:function(resp){
                if (resp.success) {
                    toastr['success'](resp.msg);
                } else {
                    toastr['error'](resp.msg);
                }
            }
        })
    }
    function getWard(district_id){
        $.ajax({
            url:'/admin/vtp/list-ward?districtId='+district_id,dataType:'json',
            success:function(resp){
                if (resp.success) {
                    toastr['success'](resp.msg);
                } else {
                    toastr['error'](resp.msg);
                }
            }
        })
    }
    function getBuuCuc(){
        $.ajax({
            url:'/admin/vtp/list-buu-cuc',dataType:'json',
            success:function(resp){
                if (resp.success) {
                    toastr['success'](resp.msg);
                } else {
                    toastr['error'](resp.msg);
                }
            }
        })
    }
    $(function(){
        var tab = '{{ in_array(request()->input('tab'),['province','district','ward'])? 'province' : (request()->input('tab')?:'account') }}';console.log(tab);
        $('a[href="#'+tab+'"]').tab('show');
        $('#frmAccount').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'/admin/vtp/login',method:'POST',data:new FormData(this),
                contentType:false,processData:false,dataType:'json',
                success:function(resp){
                    if (resp.success) {
                        toastr['success'](resp.msg);
                        $('#token').val(resp.data.token);
                        $('#userId').val(resp.data.userId);
                    } else {
                        toastr['error'](resp.msg);
                    }
                }
            })
        });
    });
</script>
@stop
