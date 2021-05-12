@extends('layout.superadmin.default')
@section('title') Admin | Doanh thu Sản phẩm @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $searchInput = '';
        if (isset($request['search_input'])) {
            $searchInput = $request['search_input'];
        }
        $breadcrumb = [
            'title' => __('Doanh thu Sản phẩm'),
            'content' => [
                __('Doanh thu Sản phẩm') => route('superadmin.report.product')
            ],
            'active' => [__('Doanh thu Sản phẩm')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script type="text/javascript">
        Common.datePicker("#start_date");
        Common.datePicker("#end_date");
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
                            <form action="{{ route('superadmin.report.product') }}" class="form-inline w-100">
                                <div class="col-md-12 text-right">
                                    <div class="form-row align-items-center">
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="start_date" name="start_date" value="{{ $from->format('d/m/Y') }}" placeholder="Ngày bắt đầu" readonly>
                                            <input type="text" class="form-control" id="end_date" name="end_date" value="{{ $to->format('d/m/Y') }}" placeholder="Ngày kết thúc" readonly>
                                            <button class="btn btn-default" type="submit"><i class="fa fa-search mr-2"></i>Tìm kiếm</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover mb-3">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>@sortablelink('shop_name', 'Tên Shop')</th>
                                    <th>@sortablelink('product_name', 'Tên sản phẩm')</th>
                                    <th>@sortablelink('product_price', 'Doanh thu')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($shopPaginate->isEmpty())
                                    <tr class="text-center">
                                        <td colspan="10">Không có dữ liệu</td>
                                    </tr>
                                @else
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($shopPaginate as $index => $shop)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $shop['shop_name'] }}</td>
                                            <td>{{ $shop['product_name'] }}</td>
                                            <td>{{ number_format($shop['product_price']) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        {{ $shopPaginate->appends($_GET)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
