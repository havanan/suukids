@extends('layout.default')
@section('title') Admin | Cấu hình EMS @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Cấu hình giao hàng'),
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
        <form method="post" action="{{ route('admin.config.ems.save') }}" enctype="multipart/form-data">
            @csrf
            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-default">
                            <h3 class="card-title">
                                Cấu hình EMS
                            </h3>
                            <div class="text-right">
                                <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-5">
                                    <label>Chọn kho</label>
                                    <select class="form-control select2" name="inventory_id" style="width: 100%;" id="inventory_id">
                                        <option value="0">Vui lòng chọn kho</option>
                                        @foreach($inventories as $key => $inventory)
                                            <option value="{{ $inventory->id }}" @if(!empty($data) && ($data->inventory_id == $inventory->id)) selected="selected" @endif> {{ $inventory->username }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-5">
                                    <label>Chọn dịch vụ</label>
                                    <select class="form-control select2" name="service_id" style="width: 100%;">
                                        <option value="0">Vui lòng chọn dịch vụ</option>
                                        @foreach($services as $key => $service)
                                            <option value="{{ $service->ems_code }}" @if(!empty($data) && ($data->service_id == $service->ems_code)) selected="selected" @endif> {{ $service->name }} </option>
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
