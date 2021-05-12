@extends('layout.default')
@section('title') Admin | Chỉnh sửa nhanh đơn hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Chỉnh sửa nhanh đơn hàng'),
            'content' => [
                __('Chỉnh sửa nhanh đơn hàng') => route('admin.sell.order.quickEdit')
            ],
            'active' => [__('Chỉnh sửa nhanh đơn hàng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)

@stop
{{-- End Breadcrumb --}}

@php
    use Carbon\Carbon;
@endphp
@section('header')
    <link href="{{ url('css/source.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('assets')
    <script>
        let getOrderHistory = "{{ route('admin.sell.order.getOrderHistory') }}";

        $(document).on('click', '#btn-save', function() {
            $('#form-quick-edit-order').submit();
        });

        Common.datePicker(".select-date");
        Common.datePicker("#start_date");
        Common.datePicker("#end_date");
        Common.formatPrice('.price');

        $(document).on('click', '.quick-edit-order-code', function() {
            let orderId = $(this).attr('data-order-code');
            let orderCode = $(this).text();
            $('#order-history-order-code').html(orderCode);
            $('#order_history_list').empty();

            $.ajax({
                type: 'GET',
                url: getOrderHistory,
                dataType: "JSON",
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    let htmlData = '';
                    if (response.data.length > 0) {
                        let dateArr = [];
                        $.each( response.data, function( key, value ) {
                            if (dateArr.indexOf(getDateFormat(value.created_at)) == -1) {
                                dateArr.push(getDateFormat(value.created_at));
                            }
                        });

                        htmlData += '<div class="timeline">';
                        $.each( dateArr, function( key, value ) {
                            let htmlItem = '<div><span class="order-history-date">'+ value +'</span></div>';

                            $.each( response.data, function( k, v ) {
                                if (value != getDateFormat(v.created_at)) {
                                    return;
                                }

                                htmlItem += '<div><i class="fas fa-file-alt bg-blue"></i><div class="timeline-item"><span class="time mr-3"><i class="fas fa-clock"></i> '+ v.created_at +'</span><h3 class="timeline-header">'+ v.user_created.account_id +'</h3><div class="timeline-body"><div class="row"><div class="col">'+ v.message +'</div></div></div></div></div>';
                            });

                            htmlData += htmlItem;
                        });
                        htmlData += '</div>';
                    } else {
                        htmlData = '<div class="empty-data-order-history">Không có lịch sử</div>';
                    }

                    $("#order_history_list").append(htmlData);
                },
                error: function(e) {
                }
            });

            $('#modal-orders-history').modal('show');
        });

        function getDateFormat(date) {
            let dateData = new Date(date);
            let year = dateData.getFullYear();
            let month = (1 + dateData.getMonth()).toString().padStart(2, '0');
            let day = dateData.getDate().toString().padStart(2, '0');

            return day + '/' + month + '/' + year;
        }
    </script>
    @include('layout.flash_message')
    <style>
        table td {
            position: relative;
            text-align: center !important; /* center checkbox horizontally */
            vertical-align: middle !important; /* center checkbox vertically */
        }

        table td input {
            position: absolute;
            display: block;
            top: 0;
            left: 0;
            margin: 0;
            height: 100% !important;
            width: 100%;
            border-radius: 0 !important;
            border: none;
            padding: 10px;
            box-sizing: border-box;
        }

        .img-product-size {
            height: 60px;
            width: 60px;
        }
        #swal2-content {
            white-space: pre-line;
        }
        .quick-edit-order-code:hover {
            cursor: pointer;
        }
    </style>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <form action="{{route('admin.sell.order.quickEdit')}}" class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control"
                                        value="{{ request()->get('search') }}" placeholder="Họ tên khách hàng hoặc số ĐT">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" name="start_date" class="form-control"
                                        value="{{ request()->get('start_date') }}" id="start_date" placeholder="Từ ngày">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" name="end_date" class="form-control"
                                        value="{{ request()->get('end_date') }}" id="end_date" placeholder="Đến ngày">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="status_id" class="form-control">
                                            <option value="">Chọn trạng thái</option>
                                            @foreach($statuses as $key => $status)
                                                <option value="{{$status->id}}" @if(request()->get('status_id') == $status->id) selected @endif>{{$status->name}}</option>
                                            @endforeach
                                        <select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-default mr-1"><i class="fas fa-search"></i>Tìm kiếm
                                    </button>
                                    <button id="btn-save" type="button" class="btn btn-primary"><i
                                            class="fa fa-save mr-2"></i>Lưu
                                        lại
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="row" style="margin:0">
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link {{ request()->get('type') != 1 ? 'active': '' }}"
                                       href="{{route('admin.sell.order.quickEdit')}}">Danh sách đơn hàng</a>
                                </div>
                            </div>
                            <form id='form-quick-edit-order' enctype='multipart/form-data' method="post"
                                  action="{{ route('admin.sell.order.quickEdit') }}">
                                @csrf
                                <div id="table" class="table-editable">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Mã ĐH</th>
                                            <th class="text-center">NV</th>
                                            <th class="text-center">Khách hàng</th>
                                            <th class="text-center">Điện thoại</th>
                                            <th class="text-center">Tổng tiền</th>
                                            <th class="text-center">Ghi chú</th>
                                            <th class="text-center">Người xác nhận</th>
                                            <th class="text-center">Ngày xác nhận</th>
                                            <th class="text-center">Người chuyển</th>
                                            <th class="text-center">Ngày chuyển</th>
                                            <th class="text-center">Trạng thái</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data) && count($data) > 0)
                                                @foreach ($data as $item)
                                                    <tr class="product-item" id="pro_{{$item->id}}">
                                                        <input type="hidden" name="edit[{{$item->id}}][id]" value="{{$item->id}}">
                                                        <td class="quick-edit-order-code" data-order-code="{{$item->id}}">
                                                            {{$item->code}}
                                                        </td>
                                                        <td>
                                                            {{ !empty($item->assigned_user) ? $item->assigned_user->name : ''}}
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="edit[{{$item->id}}][customer][id]" value="{{$item->customer->id}}">
                                                            <input type="text" name="edit[{{$item->id}}][customer][name]"
                                                                   value="{{$item->customer->name}}" @if(!getCurrentUser()->hasPermission('sub_quick_edit')) readonly @endif>
                                                        </td>
                                                        <td>
                                                            {{$item->customer->phone}}
                                                        </td>
                                                        <td>
                                                            <input type="text" name="edit[{{$item->id}}][total_price]" class="price" value="{{number_format($item->total_price)}}" @if(!getCurrentUser()->hasPermission('sub_quick_edit') && $item->total_price > 0) readonly @endif>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="edit[{{$item->id}}][note1]" value="{{$item->note1}}" @if(!getCurrentUser()->hasPermission('sub_quick_edit') && $item->note1 != null) readonly @endif>
                                                        </td>
                                                        <td>
                                                            @if(getCurrentUser()->hasPermission('sub_quick_edit'))
                                                                <select name="edit[{{$item->id}}][close_user_id]" class="form-control">
                                                                    <option value=""></option>
                                                                    @foreach ($users as $key => $user)
                                                                        <option {{$item->close_user_id == $user->id ? 'selected' : ''}}  value="{{$user->id}}">{!! $user->name !!}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <label><strong>
                                                                        @foreach ($users as $key => $user)
                                                                            @if($item->close_user_id  == $user->id)
                                                                                {!! $user->name !!}
                                                                            @endif
                                                                        @endforeach
                                                                    </strong>
                                                                </label>
                                                                <input type="hidden" name="edit[{{$item->id}}][close_user_id]" value="{{$item->close_user_id}}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(getCurrentUser()->hasPermission('sub_quick_edit'))
                                                                <input type="text" name="edit[{{$item->id}}][close_date]"
                                                                       value="{{ !empty($item->close_date) ? \Carbon\Carbon::parse($item->close_date)->format('d/m/Y') : ''}}"
                                                                       class="select-date">
                                                            @else
                                                                <span><strong>{{ !empty($item->close_date) ? \Carbon\Carbon::parse($item->close_date)->format('d/m/Y') : ''}}</strong></span>
                                                                <input type="hidden" name="edit[{{$item->id}}][close_date]" value="{{ !empty($item->close_date) ? \Carbon\Carbon::parse($item->close_date)->format('d/m/Y') : ''}}">
                                                            @endif

                                                        </td>
                                                        <td>

                                                            @if(getCurrentUser()->hasPermission('sub_quick_edit'))
                                                                <select name="edit[{{$item->id}}][delivery_user_id]" class="form-control">
                                                                    <option value=""></option>
                                                                    @foreach ($users as $key => $user)
                                                                        <option {{$item->delivery_user_id == $user->id ? 'selected' : ''}}  value="{{$user->id}}">{!! $user->name !!}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <label><strong>
                                                                        @foreach ($users as $key => $user)
                                                                            @if($item->delivery_user_id  == $user->id)
                                                                                {!! $user->name !!}
                                                                            @endif
                                                                        @endforeach
                                                                    </strong>
                                                                </label>
                                                                <input type="hidden" name="edit[{{$item->id}}][delivery_user_id]" value="{{$item->delivery_user_id}}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(getCurrentUser()->hasPermission('sub_quick_edit'))
                                                                <input type="text" name="edit[{{$item->id}}][delivery_date]"
                                                                       value="{{ !empty($item->delivery_date) ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : ''}}"
                                                                       class="select-date">

                                                            @else
                                                                <span><strong>{{ !empty($item->delivery_date) ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : ''}}</strong></span>
                                                                <input type="hidden" name="edit[{{$item->id}}][delivery_date]" value="{{ !empty($item->delivery_date) ? \Carbon\Carbon::parse($item->delivery_date)->format('d/m/Y') : ''}}">
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(getCurrentUser()->hasPermission('sub_quick_edit'))
                                                                <select name="edit[{{$item->id}}][status_id]" class="form-control">
                                                                    @foreach ($statuses as $key => $status)
                                                                        <option {{$item->status_id == $status->id ? 'selected' : ''}}  value="{{$status->id}}">{{$status->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <label><strong>
                                                                        {{isset($item->status->name) ? $item->status->name : '-'}}
                                                                    </strong>
                                                                </label>
                                                                <input type="hidden" name="edit[{{$item->id}}][status_id]" value="{{$item->status_id}}">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @if(isset($data) && count($data) > 0)
                                        {{ $data->links() }}
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.sell.order.order_history')
    </section>
@stop
