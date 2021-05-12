@extends('layout.default')
@section('title') Admin | Quản lý sản phẩm @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý sản phẩm'),
            'content' => [
                __('Quản lý sản phẩm') => route('admin.product.create')
            ],
            'active' => [__('Quản lý sản phẩm')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script src="{{url('js/source.js')}}"></script>
    <script>
        $("#image_url").change(function() {
            readImageUrl(this);
        });
        Common.formatPrice('.price');
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container">
            <form method="post" @if(isset($info)) action="{{route('admin.product.update',$info->id)}}" @else action="{{route('admin.product.store')}}" @endif enctype="multipart/form-data">
                @csrf
                @if(isset($info))
                    {{ method_field("PUT") }}
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-right">
                                <button class="btn btn-primary"><i class="fa fa-save mr-2"></i>Lưu lại</button>
                                <a href="{{route('admin.product.index')}}" class="btn btn-default"><i
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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="code">Mã sản phẩm / hàng hóa (<span class="text-danger">*</span>)</label>
                                                    <input name="code" id="code" class="form-control" type="text" value="{!! isset($info->code) ? $info->code : old('code') !!}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Tên sản phẩm / hàng hóa (<span class="text-danger">*</span>)</label>
                                                    <input name="name" id="name" class="form-control" type="text" value="{!! isset($info->name) ? $info->name : old('name') !!}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price">Giá bán (<span class="text-danger">*</span>)</label>
                                                    <input name="price" id="price" class="form-control price" type="text" value="{{ isset($info->price) ? $info->price : old('price') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="unit_id">Đơn vị</label>
                                                    <select name="unit_id" id="unit_id" class="form-control">
                                                        <option value="">Chọn</option>
                                                        @if(isset($units) && !empty($units) )
                                                            @foreach($units as $key => $item)
                                                                <option value="{{$item->id}}"
                                                                        @if(isset($info->unit_id) && $info->unit_id == $item->id || old('unit_id') == $item->id)
                                                                            selected
                                                                        @endif>{{$item->name}}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="import_price">Giá vốn (<span class="text-danger">*</span>)</label>
                                                    <input name="cost_price" id="cost_price" class="form-control price" type="text"
                                                        value="{{isset($info->cost_price) ? $info->cost_price : old('cost_price')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="on_hand">Tồn kho đầu kỳ (<span class="text-danger">*</span>)
                                                    </label>
                                                    <input name="on_hand" id="on_hand" class="form-control" type="number" value="{{isset($info->on_hand) ? $info->on_hand : old('on_hand')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning-custom">
                                            Phiếu nhập kho đầu kỳ sẽ tự động sinh khi bạn nhập số tồn đầu kỳ
                                        </div>
                                        <div class="form-group d-none">
                                            <label for="color">Màu</label>
                                            <input name="color" id="color" class="form-control" type="text" value="{!! isset($info->color) ? $info->color : old('color') !!}">
                                        </div>
                                        <div class="form-group d-none">
                                            <label for="size">Kích cỡ</label>
                                            <input name="size" id="size" class="form-control" type="text" value="{!! isset($info->size) ? $info->size : old('size') !!}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bundle_id">Loại</label>
                                                    <select name="bundle_id" id="bundle_id" class="form-control">
                                                        <option value="">Chọn</option>
                                                        @if(isset($bundles) && !empty($bundles) )
                                                            @foreach($bundles as $key => $item)
                                                                <option value="{{$item->id}}"
                                                                        @if(isset($info->bundle_id) && $info->bundle_id == $item->id || old('bundle_id') == $item->id)
                                                                        selected
                                                                    @endif>{{$item->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_care_days">Size</label>
                                                    <select name="size" id="size" class="form-control">
                                                        <option value="">Chọn size</option>
                                                        <?php
                                                        $size = SIZES
                                                        ?>
                                                        @foreach($size as $item)
                                                            <option value="{{$item}}" @if(isset($info->size) && $info->size == $item) selected  @endif>{{$item}}</option>@endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_care_days">Màu sắc</label>
                                                    <select name="color" id="color" class="form-control">
                                                        <option value="">Chọn màu</option>
                                                        @foreach(COLORS as $key => $item)
                                                            <option value="{{$key}}" @if(isset($info->color) && $info->color == $key) selected  @endif>{{$item}}</option>@endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="del">Tình trạng</label>
                                            <select name="status" id="status" class="form-control">
                                                @if(PRODUCT_STATUS && !empty(PRODUCT_STATUS) )
                                                    @foreach(PRODUCT_STATUS as $key => $item)
                                                        <option value="{{$key}}"
                                                                @if(isset($info->status) && $info->status == $key || old('status') == 1)
                                                                selected
                                                            @endif>{{$item}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="box box-default">
                                                <div class="box-header with-border">
                                                    <div class="box-title">
                                                        <h4 class="text-title">Ảnh sản phẩm</h4>
                                                    </div>
                                                    <img class="avatar-product"
                                                         src="@if(isset($info->product_image) && $info->product_image != null)
                                                            {{url($info->product_image)}}
                                                         @else {{url('theme/admin-lte/dist/img/slider-icon.png')}}
                                                         @endif"
                                                         id="imagePreview" alt="{{isset($info->name) ? $info->name : ''}}">
                                                </div>
                                                <div class="box-body">
                                                    <div class="wrapper-image text-center">
                                                    </div>
                                                    <div class="box-image-upload">
                                                        <br>
                                                        <input class="form-control" name="product_image" id="image_url"
                                                               accept="image/gif,image/jpeg,image/jpg,image/png"
                                                               type="file" >
                                                        <span class="text-danger">Ảnh < 8M (*.jpg, *.jpeg, *.gif, *.png)</span>
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
            </form>
        </div><!-- /.container-fluid -->
    </section>
@stop
