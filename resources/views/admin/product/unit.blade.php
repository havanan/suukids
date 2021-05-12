@extends('layout.default')
@section('title') Admin | Quản lý đơn vị @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý đơn vị'),
            'content' => [
                __('Quản lý đơn vị') => route('admin.unit.index')
            ],
            'active' => [__('Quản lý đơn vị')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script>
        const urlConfirm = '{{route('admin.unit.delete')}}';
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
                        <div class="card-header text-right">
                            <button class="btn btn-primary" onclick="submitForm()">Lưu lại</button>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form method="post" action="{{route('admin.unit.save')}}" id="frm-data">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="w-40">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkAll">
                                                <label for="checkAll"></label>
                                            </div>
                                        </th>
                                        <th class="w-40">ID</th>
                                        <th >Tên đơn vị</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                    @if(isset($data) && count($data) > 0)
                                        @foreach($data as $key => $item)
                                            <tr id="row{{$item->id}}">
                                                <td>
                                                    <div class="icheck-primary d-inline">
                                                        <input type="checkbox" id="checkbox{{$item->id}}" class="checkItem">
                                                        <label for="checkbox{{$item->id}}"></label>
                                                    </div>
                                                </td>
                                                <td>{{$item->id}}</td>
                                                <td>
                                                    <input type="text" value="{{$item->name}}" name="data[{{$key}}][name]" class="form-control">
                                                    <input type="hidden" name="data[{{$key}}][id]" value="{{$item->id}}">

                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-delete" onclick="deleteItem({{$item->id}})"><i class="fa fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </form>
                            <div class="text-right">
                                <button class="btn btn-primary mt-3" onclick="addNameRow()"><i class="fa fa-plus mr-2"></i>Thêm phân loại</button>

                            </div>
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
