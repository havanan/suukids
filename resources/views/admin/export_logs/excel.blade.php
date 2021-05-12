@extends('layout.default')
@section('title') Admin | Lịch sử xuất Excel @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $searchInput = '';
        if (isset($request['search_input'])) {
            $searchInput = $request['search_input'];
        }
        $breadcrumb = [
            'title' => __('Lịch sử xuất Excel'),
            'content' => [
                __('Lịch sử xuất Excel') => route('admin.export_logs.excel')
            ],
            'active' => [__('Lịch sử xuất Excel')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <form action="{{ route('admin.export_logs.excel') }}" class="form-inline w-100">
                                <div class="col-md-12 text-right">
                                    <div class="form-row align-items-center">
                                        <div class="col-12">
                                            <input type="text" class="form-control" name="search_input" value="{{ $searchInput }}" placeholder="Tìm kiếm">
                                            <button class="btn btn-default" type="submit"><i class="fa fa-search mr-2"></i>Tìm kiếm</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover mb-3">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tiêu đề</th>
                                    <th>Chi tiết</th>
                                    <th style="width: 200px;">URL</th>
                                    <th>Tài khoản</th>
                                    <th>IP</th>
                                    <th>SHOP</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($logList->isEmpty())
                                    <tr class="text-center">
                                        <td colspan="10">Không có dữ liệu</td>
                                    </tr>
                                @else
                                    @foreach ($logList as $index => $logItem)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $logItem->title }}</td>
                                            <td>{{ $logItem->detail }}</td>
                                            <td>{{ $logItem->url }}</td>
                                            <td>{{ $logItem->user_name }}</td>
                                            <td>{{ $logItem->ip }}</td>
                                            <td>{{ $logItem->shop_name }}</td>
                                            <td>{{ $logItem->created_at }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{ $logList->appends($_GET)->links() }}
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
