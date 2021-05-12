@extends('layout.default')
@php
    $columns = [
        ['code' => 'total', 'text'=>'Tổng số'],
        ['code' => 'lead', 'text'=>'Số chưa xác nhận'],//ok
        ['code' => 'deal', 'text'=>'Số xác nhận'],//ok
        ['code' => 'consider', 'text'=>'Tham khảo suy nghĩ'],//ok
        ['code' => 'untouch', 'text'=>'Không nghe máy'],//ok
        ['code' => 'busy', 'text'=>'Máy bận'],//ok
        ['code' => 'close', 'text'=>'Chuyển hàng'],//ok
        ['code' => 'return', 'text'=>'Chuyển hoàn'],//ok
        ['code' => 'success', 'text'=>'Thành công'],//ok
        ['code' => 'closed', 'text'=>'Đã thu tiền'],//ok
        ['code' => 'returned', 'text'=>'Hàng đã trả về kho'],//ok
    ];
@endphp

@section('assets')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}"/>
    <script src="{{asset('js/reports/common.js')}}"></script>
    <script src="{{asset('js/kendo.ui.core.min.js')}}"></script>
    <script>
        function updateTable(){
            $('tr[data-key]').each(function(){
                var marketer_id = $(this).data('key');
                var filtered = window.data.filter(x => x.upsale_from_user_id == marketer_id || x.user_created == marketer_id || x.marketing_id == marketer_id);
                $('.js-total-lead').html(formatNumber(formatToNumeric($('.js-total-lead').html()) + filtered.filter(x => x.status_id == {{ NO_PROCESS_ORDER_STATUS_ID }}).length));
                $(this).find('.js-lead').html(formatNumber(filtered.filter(x => x.status_id == {{ NO_PROCESS_ORDER_STATUS_ID }}).length));

                $('.js-total-deal').html(formatNumber(formatToNumeric($('.js-total-deal').html()) + filtered.filter(x => x.status_id == {{ CLOSE_ORDER_STATUS_ID }}).length));
                $(this).find('.js-deal').html(formatNumber(filtered.filter(x => x.status_id == {{ CLOSE_ORDER_STATUS_ID }}).length));

                $('.js-total-consider').html(formatNumber(formatToNumeric($('.js-total-consider').html()) + filtered.filter(x => x.status_id == {{ CONSIDER_ORDER_STATUS_ID }}).length));
                $(this).find('.js-consider').html(formatNumber(filtered.filter(x => x.status_id == {{ CONSIDER_ORDER_STATUS_ID }}).length));

                $('.js-total-untouch').html(formatNumber(formatToNumeric($('.js-total-untouch').html()) + filtered.filter(x => x.status_id == {{ UNTOUCH_ORDER_STATUS_ID }}).length));
                $(this).find('.js-untouch').html(formatNumber(filtered.filter(x => x.status_id == {{ UNTOUCH_ORDER_STATUS_ID }}).length));

                $('.js-total-busy').html(formatNumber(formatToNumeric($('.js-total-busy').html()) + filtered.filter(x => x.status_id == {{ CALL_BUSY_STATUS_ID }}).length));
                $(this).find('.js-busy').html(formatNumber(filtered.filter(x => x.status_id == {{ CALL_BUSY_STATUS_ID }}).length));

                $('.js-total-close').html(formatNumber(formatToNumeric($('.js-total-close').html()) + filtered.filter(x => x.status_id == {{ DELIVERY_ORDER_STATUS_ID }}).length));
                $(this).find('.js-close').html(formatNumber(filtered.filter(x => x.status_id == {{ DELIVERY_ORDER_STATUS_ID }}).length));

                $('.js-total-return').html(formatNumber(formatToNumeric($('.js-total-return').html()) + filtered.filter(x => x.status_id == {{ REFUND_ORDER_STATUS_ID }}).length));
                $(this).find('.js-return').html(formatNumber(filtered.filter(x => x.status_id == {{ REFUND_ORDER_STATUS_ID }}).length));

                $('.js-total-success').html(formatNumber(formatToNumeric($('.js-total-success').html()) + filtered.filter(x => x.status_id == {{ COMPLETE_ORDER_STATUS_ID }}).length));
                $(this).find('.js-success').html(formatNumber(filtered.filter(x => x.status_id == {{ COMPLETE_ORDER_STATUS_ID }}).length));

                $('.js-total-closed').html(formatNumber(formatToNumeric($('.js-total-closed').html()) + filtered.filter(x => x.status_id == {{ COLLECT_MONEY_ORDER_STATUS_ID }}).length));
                $(this).find('.js-closed').html(formatNumber(filtered.filter(x => x.status_id == {{ COLLECT_MONEY_ORDER_STATUS_ID }}).length));

                $('.js-total-returned').html(formatNumber(formatToNumeric($('.js-total-returned').html()) + filtered.filter(x => x.status_id == {{ RETURNED_STOCK_STATUS_ID }}).length));
                $(this).find('.js-returned').html(formatNumber(filtered.filter(x => x.status_id == {{ RETURNED_STOCK_STATUS_ID }}).length));

                $('.js-total-total').html(formatNumber(formatToNumeric($('.js-total-total').html()) + filtered.length));
                $(this).find('.js-total').html(formatNumber(filtered.length));
            });
        }
        function loadReport(){
            $.ajax({
                url:"/admin/report/marketing-stage",
                method:'POST',
                data:{
                    type_date:$('[name="type_date"]').val(),
                    from:$('[name="date_begin"]').val(),
                    to:$('[name="date_end"]').val(),
                },
                dataType:'json',
                success:function(resp){
                    window.data = resp.data;
                    updateTable();
                }
            });
        }
        function getMarketers(){
            $.ajax({
                url:"/admin/report/marketers",method:'POST',
                success:function(resp){
                    window.marketers = resp.data;
                    window.wage = resp.wage;
                    loadReport();
                }
            })
        }
        $(function(){
            getMarketers();

        });
    </script>
@stop
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h3 class="alert alert-info">Báo cáo kho số MKT</h3>
                            </div>
                        </div>
                        <p class="alert alert-info">
                        Mọi người cần theo dõi data số của mình để có tỷ lệ chốt cao nhất
                        </p>
                        <form action="">
                            <div class="row">
                                <div class="col-4 col-md-2">
                                    <input value="{{ array_get($filters,'date_begin', date('01/m/Y')) }}" name="date_begin" class="form-control datepicker">
                                </div>
                                <div class="col-4 col-md-2">
                                    <input value="{{ array_get($filters,'date_end', date('d/m/Y')) }}" name="date_end" class="form-control datepicker">
                                </div>
                                <div class="col-4 col-md-2">
                                    <select name="type_date" class="form-control">
                                        <option value="created_at" {{ array_get($filters,'type_date') === 'created_at' ? 'selected' : '' }}>Ngày tạo đơn</option>
                                        <option value="close_date" {{ array_get($filters,'type_date') === 'close_date' ? 'selected' : '' }}>Ngày chốt đơn</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    <button id="btnExport" type="button" onclick="fnExcelReport('ReportTable');" class="btn btn-info"><i class="fa fa-download"></i> Xuất Excel </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div id="html">
                            <iframe id="txtArea1" style="display:none"></iframe>
                            <table id="ReportTable" width="100%" class="table table-bordered table-sm" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                                <thead>
                                    <tr>
                                        <th>MKT</th>
                                        @foreach($columns as $column)
                                        <th>{{ $column['text'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marketers as $marketer_id => $marketer)
                                    <tr data-key="{{ $marketer_id }}">
                                        <th>{{ $marketer }}</th>
                                        @foreach($columns as $column)
                                        <td class="text-center js-{{ $column['code'] }}" data-key="{{ $column['code'] }}"></td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Tổng</th>
                                        @foreach($columns as $column)
                                        <td class="text-center js-total-{{ $column['code'] }}"></td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
