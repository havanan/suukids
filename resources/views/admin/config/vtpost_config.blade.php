@extends('layout.default')
@section('title') Admin | Cấu hình VTPost @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
        'title' => __('Cấu hình kho và dịch vụ'),
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
            <form method="post" action="{{ route('admin.config.vtpost.config.save') }}" enctype="multipart/form-data">
            @csrf
            <!-- /.row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-default">
                                <h3 class="card-title">
                                    Cấu hình VTPost
                                </h3>
                                <div class="text-right">
                                    @if (auth()->user()->isAdmin())
                                        <a class="btn btn-primary text-white" href="{{ route('admin.config.vtpost.shop.index') }}"><i class="fa fa-plus mr-2"></i>Tạo shop</a>
                                    @endif
                                    <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Chọn kho</label>
                                        <select class="form-control select2" name="group_address_id" style="width: 100%;" id="inventory_id">
                                            <option>Vui lòng chọn kho</option>
                                            @foreach($stores as $inventory)
                                                <option value="{{ $inventory->group_address_id }}" {{ !empty($config) ? $config->group_address_id == $inventory->group_address_id ? 'selected' : '' : ''  }}>{{ $inventory->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Chọn dịch vụ</label>
                                        <select class="form-control select2" name="service_code" style="width: 100%;">
                                            @foreach($services as $service)
                                                <option value="{{ $service->service_code }}" {{ !empty($config) ? $config->service_code == $service->service_code ? 'selected' : '' : ''  }}>{{ $service->name }}</option>
                                            @endforeach
                                        </select>
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
