@extends('layout.default')
@section('title') Admin | Quản lý xuất kho @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý xuất kho'),
            'content' => [
                __('Quản lý xuất kho') => route('list.bill.export')
            ],
            'active' => [__('Quản lý xuất kho')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <link rel="stylesheet" href="{{asset('theme/admin-lte/dateplugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">

    <script src="{{asset('theme/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{asset('theme/admin-lte/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script>
        // $("#example1").DataTable();
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
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="text-right">
                                        <a href="{{route('stock.stock_out.create')}}" class="btn btn-primary"><i
                                                class="fa fa-plus mr-2"></i> Thêm phiếu</a>
                                        <button class="btn btn-success">Xuất nội bộ</button>
                                        <button class="btn btn-danger">Xóa</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <form action="{{route('list.bill.export')}}" action="post">
                                    @csrf
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="bill_number" placeholder="Số phiếu">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="note" placeholder="Diễn dải">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="date" class="form-control" name="date_from" placeholder="Từ ngày">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="date" class="form-control" name="date_to" placeholder="Đến ngày">
                                            </div>
                                            <div class="col-md-2">
                                                <select class="form-control" name="supplier_id">
                                                    <option value="">Nhà cung cấp</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select class="form-control">
                                                    <option value="">Kho tổng</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-left">
                                        <button type="submit" class="btn btn-info"><i class="fa fa-search mr-2"></i>Tìm kiếm</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="w-40"><input type="checkbox"></th>
                                    <th class="w-40">STT</th>
                                    <th>Ngày tạo</th>
                                    <th>Số phiếu</th>
                                    <th>Người xuất</th>
                                    <th>Người nhận</th>
                                    <th>Diễn giải</th>
                                    <th>Tổng tiền</th>
                                    <th class="w-40"></th>
                                    <th class="w-40"></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td class="text-center">1</td>
                                        <td>
                                           <p>04/03/2020</p>
                                        </td>
                                        <td>
                                           <p>PX1</p>
                                        </td>
                                        <td>
                                          <p> abcd1234</p>
                                        </td>
                                        <td>
                                            <p>alula mama</p>
                                        </td>
                                        <td>
                                            <p>alula mama</p>
                                        </td>
                                        <td>
                                           <p>0</p>
                                        </td>

                                        <td class="text-center">
                                            <button class="btn btn-default">Xem</button>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-warning text-white">Sửa</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-right">
                                            <h4 class="text-danger"><strong>Tổng tiền</strong></h4>
                                        </td>
                                        <td colspan="3">
                                            <h4 class="text-danger"><strong>0</strong></h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
