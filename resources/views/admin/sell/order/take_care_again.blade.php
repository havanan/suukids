@extends('layout.default')
@section('title') Admin | Danh sách chăm sóc lại @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Danh sách chăm sóc lại'),
            'content' => [
                __('Danh sách chăm sóc lại') => route('admin.sell.order.take-care-again')
            ],
            'active' => [__('Danh sách chăm sóc lại')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)

@stop
{{-- End Breadcrumb --}}
@section('header')
    <link href="{{ url('css/source.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('assets')
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
    <script>
        $(document).on("click",".btn-called",function(){
            var url = $(this).data("url");
            Swal.fire({
                title: "Cảnh báo !",
                text: "Bạn chắc chắn là đã gọi?",
                type: "warning",
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonColor: "#007bff",
                confirmButtonText: "Chắc chắn",
                cancelButtonText: "Hủy",
                closeOnConfirm: false,
                closeOnCancel: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'GET',
                        url: url,
                        dataType: "JSON",
                        success: function(response) {
                            alertPopup('success', "Cập nhật thành công!");
                            setTimeout(function(){
                                window.location.reload();
                            },1000)
                        },
                        error: function(e) {
                            alertPopup('error', "Có lỗi xảy ra, hãy thử lại.");
                        }
                    });
                }
            });
        })
    </script>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="">
                <div class="float-right">
                    <a class="btn btn-primary" href="{{ route('admin.sell.order.index') }}"><i class="fas fa-list"></i> Trở lại</a>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="table" class="table-editable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">Mã ĐH</th>
                                        <th class="text-center">Sản phẩm</th>
                                        <th class="text-center">Khách hàng</th>
                                        <th class="text-center">Điện thoại</th>
                                        <th class="text-center">Tổng tiền</th>
                                        <th class="text-center">Ngày thành công</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data) && count($data) > 0)
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td class="text-center" style="">
                                                        <button @if(!empty($item->called)) disabled @endif data-url="{{ route('admin.sell.order.take-care-again/called', $item->id) }}" class="btn btn-warning text-white mb-2 btn-called"> Đã gọi </i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        {{$item->code}}
                                                    </td>
                                                    <td>
                                                        {{ $item->product_quantity}} {{$item->product_name}}
                                                    </td>
                                                    <td>
                                                        {{ $item->customer_name}}
                                                    </td>
                                                    <td>
                                                        {{$item->customer_phone}}
                                                    </td>
                                                    <td>
                                                        {{number_format($item->total_price)}} đ
                                                    </td>
                                                    <td>
                                                        {{$item->complete_date}}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

