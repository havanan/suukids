@extends('layout.superadmin.default')
@section('title') Thay đổi mật khẩu @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Thay đổi mật khẩu'),
            'content' => [
                __('Thay đổi mật khẩu') => ''
            ],
            'active' => [__('Thay đổi mật khẩu')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
@stop
@section('content')
    <section class="content">
        <div class="container">
            <form method="post" action="{{ route('superadmin.profile.change_password') }}">
                @csrf
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
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="old_password">Mật khẩu cũ(<span class="text-danger">*</span>)</label>
                                    <input name="old_password" id="old_password" class="form-control" type="password">
                                </div>
                                <div class="form-group">
                                    <label for="password">Mật khẩu mới(<span class="text-danger">*</span>)</label>
                                    <input name="password" id="password" class="form-control" type="password">
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Xác nhận mật khẩu mới(<span class="text-danger">*</span>)</label>
                                    <input name="password_confirmation" id="password_confirmation" class="form-control" type="password">
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
