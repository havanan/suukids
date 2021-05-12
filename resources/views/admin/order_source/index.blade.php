@extends('layout.default')
@section('title') Admin | Quản lý nguồn đơn hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý nguồn đơn hàng'),
            'content' => [
                __('Quản lý nguồn đơn hàng') => route('admin.order_source.index')
            ],
            'active' => [__('Quản lý nguồn đơn hàng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script>
        const urlConfirm = '{{route('admin.order_source.delete')}}';
    </script>
    <script src="{{url('js/source.js')}}"></script>
    @include('layout.flash_message')
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- /.modal -->
                            <div class="text-center" style="float: right">
                                <button class="btn btn-default btn-save" onclick="submitForm()">
                                    <h4><i class="fa fa-save"></i></h4>
                                    <span class="text-primary">Ghi lại</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form method="post" action="{{route('admin.order_source.save')}}" id="frm-data">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="w-40">ID</th>
                                        <th >Nguồn</th>
                                        <th class="text-center">Mặc định</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                    @if(isset($data) && count($data) > 0)
                                        @foreach($data as $key => $item)
                                            <tr @if($item->is_system == 0) id="row{{$item->id}}" @endif>
                                                <td>{{$item->id}}</td>
                                                <td>
                                                    <input type="text" value="{{$item->name}}" class="form-control" name="data[{{$key}}][name]" @if($item->is_system == 1) disabled @endif >
                                                    <input type="hidden" name="data[{{$key}}][id]" value="{{$item->id}}">
                                                </td>
                                                <td class="text-center">
                                                    <div class="icheck-primary d-inline">
                                                        <input type="checkbox" id="checkbox{{$key}}" name="data[{{$key}}][default_select]" value="1" @if($item->default_select == 1) checked @endif>
                                                        <label for="checkbox{{$key}}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($item->is_system == 0)
                                                        <button type="button" class="btn btn-danger btn-delete" onclick="deleteItem({{$item->id}})"><i class="fa fa-trash-alt"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </form>

                            <button class="btn btn-primary mt-3" onclick="addRow()">Thêm</button>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop
