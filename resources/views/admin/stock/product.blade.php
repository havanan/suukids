@extends('layout.default')
@section('title') Admin | Báo cáo tồn kho @stop

@section('assets')

<script>
    $(function() {
        $('[data-action="view-detail"]').click(function() {
            $('[name="product_id"]').val(this.dataset.key);
            $('#frmProduct').submit();
        });
        $('#frmProduct').on('submit',function(e){
            e.preventDefault();
            $.ajax({
                url:'/admin/stock/product',method:'POST',
                data:new FormData(this),
                contentType:false,processData:false,
                dataType:'json',
                success:function(resp) {
                    if (resp.success) {
                        for(var i=0;i < resp.data.length; i++) {
                            var item = resp.data[i];
                            $(`[data-key="${item[0]}"] [data-index="1"]`).text(formatNumber(item[1]||'0'));
                            $(`[data-key="${item[0]}"] [data-index="2"]`).text(formatNumber(item[2]||'0'));
                            $(`[data-key="${item[0]}"] [data-index="3"]`).text(formatNumber(item[3]||'0'));
                            $(`[data-key="${item[0]}"] [data-index="4"]`).text(formatNumber(item[1]+item[2]-item[3]));
                        }
                    }
                }
            })
        })
    });
</script>
@stop

@section('content')
<form action="" id="frmProduct">
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="alert alert-info">Báo cáo tồn kho</h3>
                        </div>
                    </div>
                    <div class="row date-filter-box">
                        <div class="col-md-2">
                            <input value="{{ date('01/m/Y') }}" name="from" type="date" class="form-control ">
                        </div>
                        <div class="col-md-2">
                            <input value="{{ date('d/m/Y') }}" name="to" type="date" class="form-control ">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Tìm kiếm</button>
                            <input type="hidden" name="product_id" value="">
                        </div>
                    </div>
                    <hr>
                    <div id="html">
                        <iframe id="txtArea1" style="display:none"></iframe>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                                    <thead>
                                        <tr>
                                            <th>Mã hàng</th>
                                            <th>Tên vật tư hàng hóa</th>
                                            <th class="text-center">ĐVT</th>
                                            <th class="text-center">Tồn đầu kỳ</th>
                                            <th class="text-center">Nhập trong kỳ</th>
                                            <th class="text-center">Xuất trong kỳ</th>
                                            <th class="text-center">Tồn cuối kỳ</th>
                                            <!-- <th><button type="button" data-action="view-detail" data-key="" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> Tất cả</button></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                        <tr data-key="{{ $product->id }}">
                                            <td>{{ $product->code }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td class="text-center">{{isset($product->productUnit->name) ? $product->productUnit->name : ''}}</td>
                                            <td class="text-center" data-index="1">0</td>
                                            <td class="text-center" data-index="2">0</td>
                                            <td class="text-center" data-index="3">0</td>
                                            <td class="text-center" data-index="4">0</td>
                                            <!-- <td>
                                                <button type="button" data-action="view-detail" data-key="{{ $product->id }}" class="btn btn-xs btn-success"><i class="fa fa-eye"></i> Xem chi tiết</button>
                                            </td> -->
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(false)
                            <div class="col-md-9">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Giá bán</th>
                                            <th>Số lượng bán ra</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr>
                                            <td>{{ $products->where('id', $item->product_id)->first() ? $products->where('id', $item->product_id)->first()->name : 'N/A' }}</td>
                                            <td>{{ number_format($item->price,0) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->product_id }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div>
                                    {{ $items->appends($filters)->links() }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@stop
