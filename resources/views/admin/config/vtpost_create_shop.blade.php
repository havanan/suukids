@extends('layout.default')
@section('title') Admin | Cấu hình VTPost @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
        'title' => __('Cấu hình giao VTPost'),
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script src="{{ url("js/function.js") }}"></script>
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker.css')}}">
    <link rel="stylesheet"
          href="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker-regularfont.css')}}">
    <script src="{{url('theme/admin-lte/plugins/simplecolorpicker/jquery.simplecolorpicker.js')}}"></script>

    <script>
        $(function () {
            $('#province-select').select2({
                placeholder: "Chọn tỉnh thành"
            });

            $('#district-select').select2({
                placeholder: "Chọn quận/huyện",
                ajax: {
                    url: '{{route('admin.address.district.api-search')}}',
                    data: function (params) {
                        var query = {
                            province_id: $('#province-select').val(),
                            name: params.term,
                            page: params.page || 1
                        }

                        // Query parameters will be ?search=[term]&page=[page]
                        return query;
                    }
                }
            });

            $('#ward-select').select2({
                placeholder: "Chọn phường xã",
                ajax: {
                    url: '{{route('admin.address.ward.api-search')}}',
                    data: function (params) {
                        var query = {
                            district_id: $('#district-select').val(),
                            name: params.term,
                            page: params.page || 1
                        }
                        // Query parameters will be ?search=[term]&page=[page]
                        return query;
                    }
                }
            });
        });

        function onSaveAddress() {
            let ward = $('#ward-select').select2('data')[0].text;
            let district = $('#district-select').select2('data')[0].text;
            let province = $('#province-select').select2('data')[0].text;
            let address = " , " + ward + ", " + district + ", " + province;
            $('#customer_address').val(address);
            $('#customer_province').val(province);
        }
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
                                    <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Tên shop</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Số điện thoại</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Địa chỉ</label>
                                        <input type="text" name="address" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="customer_address">Tỉnh/Thành
                                            phố</label>
                                        <div class="input-group input-group-md">
                                            <input type="text" name="customer_province" id="customer_province"
                                                   class="form-control">
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-info btn-flat"
                                                        data-toggle="modal" data-target="#modal-province">Chọn tỉnh
                                                    thành</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('admin.sell.order.province')
            </form>

        </div><!-- /.container-fluid -->
    </section>
@stop
