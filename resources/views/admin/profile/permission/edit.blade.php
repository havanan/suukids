@extends('layout.default')
@section('title') Admin | Thêm mới nhóm quyền @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Chỉnh sửa nhóm quyền'),
            'content' => [
                __('Danh sách nhóm quyền') => route('admin.profile.permission.index')
            ],
            'active' => [__('Chỉnh sửa nhóm quyền')]
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

    <script>
        let updateURL = "{{route('admin.profile.permission.update', $data->id)}}"
        jQuery('#submit_btn').on('click', function () {
            let data = new FormData(jQuery("#edit-form")[0]);
            $.ajax({
                url:updateURL,
                type: "POST",
                data: data,
                contentType: false,
                processData: false,
                dataType:"JSON",
                success: function (response) {
                    toastr.success(response.message);
                    window.location.href = response.url
                }
            }).fail(function(error){
                toastr.error(error.responseJSON.message);
            })
        });
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-right">
                            <button class="btn btn-primary" id="submit_btn">Lưu lại</button>
                            <a href="{{route('admin.profile.permission.index')}}" class="btn btn-default">Danh sách</a>
                        </div>
                        <!-- /.card-header -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <form id="edit-form" action="{{route('admin.profile.permission.update', $data->id)}}" method="post" class="row">
                @csrf
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-orange">Tên nhóm quyền<span class="text-danger ml-2">*</span></label>
                                        <input type="text" class="form-control" name="name" required value="{{$data->name}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Quyền được</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                @foreach(PERMISSIONS_TITLE as $key => $value)
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" @if($data[$key]) checked  @endif name="{{$key}}" id="checkbox_{{$key}}" value="{{$key}}">
                                            <label for="checkbox_{{$key}}" class="custom-control-label">{{$value}}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Trạng thái</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th width="60%">Trạng thái</th>
                                    <th>Quyền
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control" onchange="$('.status-item').val($(this).val());">
                                                    <option value="">Áp dụng cho tất cả</option>
                                                    <option value="0">Không chọn</option>
                                                    @foreach(ORDER_STATUS_PERMISSIONS_TITLE as $key => $value)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                @foreach($orderStatus as $key => $status)
                                    <tr>
                                        <td><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background-color:{{$status->color}}"></span> {{ $status->level }}.{{ $status->name }}</td>
                                        <td>
                                            <div class="form-group">
                                                <select class="form-control status-item" name="status_{{$status->id}}">
                                                    <option value="0">Không chọn</option>
                                                    @foreach(ORDER_STATUS_PERMISSIONS_TITLE as $key2 => $value)
                                                        <option value="{{$key2}}" @if(!empty($statusPermissions[$status->id]) && $statusPermissions[$status->id] == $key2) selected @endif>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </form>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop
