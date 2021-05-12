@extends('layout.default')
@section('title') Admin | Danh sách khách hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Danh sách khách hàng'),
'content' => [
__('Danh sách khách hàng') => route('admin.customer.index')
],
'active' => [__('Danh sách khách hàng')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<link rel="stylesheet" href="{{ url('css/source.css') }}">
<link href="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet" type="text/css">
<script src="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{url('js/customer/index.js')}}"></script>
@include('layout.flash_message')
<script>
    const urlStoreCall = '{{route('admin.customer.save.call')}}';
    const urlStoreNote = '{{route('admin.customer.save.note')}}';
    const urlList = '{{route('admin.customer.index')}}';
    const urlGetHistoryCall = '{{route('admin.customer.history.call')}}';
    const urlGetHistoryNote = '{{route('admin.customer.history.note')}}';
    const CUSTOMER_EMOTIONS = JSON.parse('{!! json_encode(CUSTOMER_EMOTIONS) !!}');
    const urlDelete = '{{ route('admin.customer.delete') }}'
</script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                                <form action="{{ route('admin.sell.order.call-history-cloudfone') }}" action="get" style="display:contents">
                                <input type="hidden" value="{{ $phone }}" name="phone">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input type="text" id="created_from" class="form-control datepicker" name="created_from"
                                                placeholder="Từ ngày" value="{{ request()->get('created_from') }}"
                                            >
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="created_to" class="form-control datepicker" name="created_to"
                                            placeholder="Đến ngày" value="{{ request()->get('created_to') }}"
                                        >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <button class="btn btn-default" type="submit"><i class="fa fa-search mr-2"></i>Tìm
                                        kiếm</button>
                                    <a href="{{ route('admin.sell.order.call-history-cloudfone') }}?phone={{ $phone }}" class="btn btn-default"><i
                                            class="fa fa-refresh mr-2"></i>Tìm lại</a>
                                    {{-- <button class="btn btn-default"><i class="fa fa-refresh mr-2"></i>Tìm lại</button> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover mb-3">
                            <thead>
                                <tr>
                                    <td width="1%">STT</td>
                                    <th class="text-center">Ngày gọi</th>
                                    <th class="text-center">Đầu số</th>
                                    <th class="text-center">Số nhận</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Tổng thời gian gọi</th>
                                    <th class="text-center">Ghi âm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($listHistory))
                                    <tr class="text-center">
                                        <td colspan="10">Không có dữ liệu</td>
                                    </tr>
                                @else
                                    @foreach ($listHistory as $index => $history)
                                        <tr>
                                            <td class="text-left">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="text-center">
                                                {{ $history->ngayGoi }}
                                            </td>
                                            <td class="text-center">
                                                {{ $history->dauSo }}
                                            </td>
                                            <td class="text-center">
                                                {{ $history->soNhan }}
                                            </td>
                                            <td class="text-center">
                                                {{ $history->trangThai }}
                                            </td>
                                            <td class="text-center">
                                                {{ $history->tongThoiGianGoi }}
                                            </td>
                                            <td>
                                                @if(!empty($history->linkFile))
                                                    <a href="{{ $history->linkFile }}"> Xem file </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @if(!empty($paginator))
                        {{ $paginator->links() }}
                        @endif
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
@stop
