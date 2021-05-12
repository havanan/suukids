@extends('layout.default')
@section('title') Admin | Cấu hình tài khoản Viettel Post @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
        'title' => __('Cấu hình EMS'),
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker.css')}}">
    <link rel="stylesheet"
          href="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker-regularfont.css')}}">
    <script src="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker.js')}}"></script>

    <script>
        // $("#inventory_id").select2({
        //     placeholder: "Chọn kho",
        // });
    </script>

    @include('layout.flash_message')
@stop

@section('content')
    <section class="content">
        <div class="container">
            <form method="post" action="{{ route('admin.config.ems.savetoken') }}" enctype="multipart/form-data">
            @csrf
            <!-- /.row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-default">
                                <h3 class="card-title">
                                    Cấu hình token EMS
                                </h3>
                                <div class="text-right">
                                    <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-5">
                                        <label>Token</label>
                                        <input type="text" name="token" class="form-control" value="{{ $data['token'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div><!-- /.container-fluid -->
    </section>
@stop
