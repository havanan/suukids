@extends('layout.default')
@section('title') Admin | Thêm kho mới @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Thêm kho mới'),
'content' => [
__('Thêm kho mới') => route('admin.stock.warehouse.add')
],
'active' => [__('Thêm kho mới')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
@stop

@section('content')
<section class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Lỗi!</h5>
                    @foreach ($errors->all() as $error)
                    <div>{{$error}}</div>
                    @endforeach
                </div>
                @endif
                <form
                    action="{{ isset($warehouse) ? route('admin.stock.warehouse.edit',$warehouse->id) : route('admin.stock.warehouse.add') }}"
                    method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header text-right">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            <a href="{{ route('admin.stock.warehouse.list') }}"><button type="button"
                                    class="btn btn-danger">Quay lại</button></a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">Tên kho(*)</label>
                                <div class="col-sm-9">
                                    <input type="text" value="{{ isset($warehouse) ? $warehouse->name : old('name')}}"
                                        name="name" class="form-control" id="inputPassword" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-3 col-form-label">Kho chính (kho bán
                                    hàng):</label>
                                <div class="col-sm-9">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" @if(isset($warehouse) && $warehouse->is_main == ACTIVE)
                                        checked @endif name="is_main" value="1" class="custom-control-input"
                                        id="customCheck1" >
                                        <label class="custom-control-label" for="customCheck1"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </form>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </div><!-- /.container-fluid -->
</section>
@stop
