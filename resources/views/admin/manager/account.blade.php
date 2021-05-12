@extends('layout.default')
@section('title') Admin | Quản lý nhóm người dùng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý nhóm người dùng'),
            'content' => [
                __('Quản lý nhóm người dùng') => route('admin.manager.account')
            ],
            'active' => [__('Quản lý nhóm người dùng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script>
        function addRow() {
            var d = new Date();
            var id = d.getTime();

            var row = $('<tr id="row'+id+'">' +
                '<td></td>' +
                '<td><input type="text" class="form-control" name="name"></td>' +
                '<td class="text-center">' +
                '<select class="form-control">' +
                '    <option>option 1</option>' +
                '    <<option>option 2</option>' +
                '</select></td>' +
                '<td class="text-center"><button class="btn btn-danger" onclick="removeRow('+id+')"><i class="fa fa-trash-alt"></i></button></td>' +
                '</tr>');

            $('#order-source-body').append(row);
        }
        function removeRow(id) {
            console.log(id)
            if(id){
                $('#row'+id).remove();
            }

        }
    </script>
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
                                <button class="btn btn-default btn-save">
                                    <button type="button" class="btn btn-primary btn-custom-color float-right"><i class="far fa-save"></i> Ghi lại</button>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body row">
                            <div class="col-md-7">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 40px">ID</th>
                                        <th >Tên nhóm</th>
                                        <th class="text-center">Trưởng nhóm</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <input type="text" value="FB Comment" class="form-control">
                                            </td>
                                            <td class="text-center">
                                                <select class="form-control">
                                                    <option>option 1</option>
                                                    <option>option 2</option>
                                                    <option>option 3</option>
                                                    <option>option 4</option>
                                                    <option>option 5</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button class="btn btn-success btn-sm mt-3" onclick="addRow()">Thêm</button>
                            </div>
                            <div class="col-md-5">
                                <div class="callout callout-warning">
                                    <h5>Trưởng nhóm sale yêu cầu:</h5>
                                    <p>- Có quyền sale</p>
                                    <p>- Thuộc vào nhóm cần quản lý</p>
                                </div>
                                <div class="callout callout-warning">
                                    <h5>Trưởng nhóm marketing yêu cầu:</h5>
                                    <p>- Có quyền marketing</p>
                                    <p>- Thuộc vào nhóm cần quản lý</p>
                                </div>
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
