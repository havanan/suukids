@extends('layout.default')
@section('title') Admin | Quản lý nhóm người dùng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý nhóm người dùng'),
            'content' => [
                __('Quản lý nhóm người dùng') => route('admin.user_group.index')
            ],
            'active' => [__('Quản lý nhóm người dùng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script>
        var users = '<?php echo json_encode($users) ?>';
        users = JSON.parse(users);
    </script>
    <link rel="stylesheet" href="{{url('theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script src="{{url('js/source.js')}}"></script>
    <script src="{{url('js/user.js')}}"></script>

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
                                <button class="btn btn-default btn-save">
                                    <button type="button" class="btn btn-primary btn-custom-color float-right" onclick="submitForm()"><i class="far fa-save"></i> Ghi lại</button>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body row">
                            <div class="col-md-7">
                                <form method="post" action="{{route('admin.user_group.save')}}" id="frm-data">
                                    @csrf
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="w-40">ID</th>
                                            <th >Tên nhóm</th>
                                            <th class="text-center">Trưởng nhóm</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody id="order-source-body">
                                        @if(isset($data) && count($data) > 0)
                                            @foreach($data as $key => $item)
                                                <tr id="row{{$item->id}}">
                                                    <td>{{$item->id}}</td>
                                                    <td>
                                                        <input type="hidden" value="{{$item->id}}" name="data[{{$key}}][id]" class="form-control">
                                                        <input type="text" value="{{$item->name}}" name="data[{{$key}}][name]" class="form-control">
                                                    </td>
                                                    <td class="text-center">
                                                        <select class="form-control" name="data[{{$key}}][admin_user_id]">
                                                            <option value="">Chưa chọn</option>
                                                            @if(isset($users) && count($users) > 0)
                                                                @foreach($users as $user)
                                                                    <option value="{{$user->id}}" @if($user->id == $item->admin_user_id) selected @endif>{{$user->name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-danger" onclick="removeRow({{$item->id}})"><i class="fa fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </form>

                                <button class="btn btn-success btn-sm mt-3" type="button" onclick="addUserGroupRow()">Thêm</button>
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
