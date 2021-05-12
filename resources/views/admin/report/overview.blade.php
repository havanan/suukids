@extends('layout.default')
@php
    $columns = \App\Models\Order::getMktOverviewIndex();
@endphp
@section('assets')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}"/>
    <script src="{{asset('js/reports/common.js')}}"></script>
    <script src="{{asset('js/kendo.ui.core.min.js')}}"></script>
    <script>
        function update(elm){
            var begin = $('[name="date_begin"]').val();
            var end = $('[name="date_end"]').val();
            var key = `${begin.slice(3)}`;

            var marketer_id = $(elm).parents('tr').data('key');
            var column = elm.name.replace(/\[\]/gm,'');
            var value = ['labor_day'].includes(column) ? elm.value : formatToNumeric(elm.value);

            $.ajax({
                url:"/admin/report/update-marketer",method:'POST',data:{key:key,value: value,column:column,marketer_id:marketer_id},
                success:function(resp) {
                    getMarketers();
                }
            })

        }
        function updateTable(){
            var begin = $('[name="date_begin"]').val();
            var key = `${begin.slice(3)}`;
            var columns = ['cost','labor_day','bonus','compensation','wage','bonus_percent'];
            $('tr[data-key]').each(function(){
                var marketer_id = this.dataset.key;
                for(var i=0;i<columns.length;i++){
                    var labor = window.marketers.find(x => x.id == marketer_id).labor;
                    var mkt_cost = window.marketers.find(x => x.id == marketer_id).mkt_cost;
                    var bonus_percent = window.marketers.find(x => x.id == marketer_id).bonus_percent||{};
                    var bonus = window.marketers.find(x => x.id == marketer_id).bonus||{};
                    labor = labor[key]||{};
                    var value = labor[columns[i]]||'0';
                    var begin = $('[name="date_begin"]').val();
                    var end = $('[name="date_end"]').val();
                    var key_month = begin.slice(3);
                    if (['labor_day'].includes(columns[i])) {
                        $(this).find(`.js-${columns[i]} input`).val((value));
                    } else if (['bonus_percent'].includes(columns[i])) {
                        $(this).find(`.js-${columns[i]} input`).val(formatNumber(bonus_percent[key_month]||0));
                    } else if (['bonus'].includes(columns[i])) {
                        $(this).find(`.js-${columns[i]} input`).val(formatNumber(bonus[key_month]||0));
                    } else if (['cost'].includes(columns[i])) {
                        var total_cost = 0;
                        Object.keys(mkt_cost).forEach(source => {
                            var items = mkt_cost[source];
                            Object.keys(items).forEach(date => {
                                if (isDateBetween(date,begin,end)) {
                                    total_cost += parseInt(items[date]);
                                }
                            });
                        });
                        $(this).find(`[data-key="${columns[i]}"]`).text(formatNumber(total_cost));
                    } else if (['compensation'].includes(columns[i])){
                            $(this).find(`.js-${columns[i]} input`).val(formatNumber(value>0?value:500000));
                    }else if (['wage'].includes(columns[i])) {
                        var wage = window.wage[marketer_id];
                        $(this).find(`.js-${columns[i]} input`).val(formatNumber(wage||value));
                    } else {
                        $(this).find(`.js-${columns[i]} input`).val(formatNumber(value));
                    }
                }

                var filtered = window.data.filter(x => x.marketing_id == marketer_id || x.upsale_from_user_id == marketer_id || x.user_created == marketer_id);
                var orders = filtered.filter(x => [10].includes(x.status_id));
                var lead = filtered.length;
                var cost = formatToNumeric($(this).find('.js-cost input').val());
                var cpl = Math.round(lead/cost*100);
                var orders_total = orders.map(x => x.total_price);
                orders_total.push(0);
                var revenue = orders_total.reduce((a,b)=>a+b);
                var rate_of_return = revenue ? (cost/revenue*100).toFixed(2) : 0;
                var bonus_percent = formatToNumeric($(this).find('[data-key="bonus_percent"] input').val());

                $(this).find(`[data-key="lead"]`).text(formatNumber(lead));
                $(this).find(`[data-key="cost_per_lead"]`).text(!cost ? 'N/A' : (cpl));
                $(this).find(`[data-key="orders"]`).text(formatNumber(filtered.filter(x => [10].includes(x.status_id)).length));
                $(this).find(`[data-key="rpu"]`).text(filtered.length?formatNumber(Math.ceil(orders_total.reduce((a,b)=>a+b)/filtered.length/1000)*1000):'0');
                $(this).find(`[data-key="revenue"]`).text(formatNumber(revenue));
                $(this).find(`[data-key="close_rate"]`).text(lead?(orders.length/lead*100).toFixed(2):'N/A');
                $(this).find(`[data-key="rate_of_return"]`).text(revenue?rate_of_return:'N/A');

                var labor_day = parseFloat($(this).find('.js-labor_day input').val());
                var wage = formatToNumeric($(this).find('.js-wage input').val());
                var bonus = formatToNumeric($(this).find('.js-bonus input').val());
                var labor_wage = Math.round(wage/26*labor_day/1000)*1000;
                $(this).find(`[data-key="total_wage"]`).text(labor_day&&wage?formatNumber(labor_wage):'N/A');
                $(this).find(`[data-key="total"]`).text(labor_day&&wage?formatNumber(labor_wage+bonus+Math.round(revenue*bonus_percent/100)):'N/A');
            });
        }
        function loadReport(){
            $.ajax({
                url:"{{ route('admin.report.overview') }}",
                method:'POST',
                data:{
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
                url:"{{ route('admin.report.marketers') }}",method:'POST',
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
                                <h3 class="alert alert-info">Báo cáo Lương MKT theo tháng</h3>
                            </div>
                        </div>
                        <form action="">
                            <div class="row">
                                <div class="col-md-2">
                                    <input value="{{ array_get($filters,'date_begin', date('01/m/Y')) }}" name="date_begin" class="form-control datepicker">
                                </div>
                                <div class="col-md-2">
                                    <input value="{{ array_get($filters,'date_end', date('d/m/Y')) }}" name="date_end" class="form-control datepicker">
                                </div>
                                <div class="col-md-2">
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
                                        @if(array_get($column,'type')=='input')
                                        <td data-key="{{ $column['code'] }}" class="js-{{ $column['code'] }}">
                                            <input style="width:100px;" name="{{ $column['code'] }}[]" class="form-control text-right {{ $column['code']=='labor_day'? '': 'js-currency' }}" onchange="update(this)">
                                        </td>
                                        @else
                                        <td class="text-right" data-key="{{ $column['code'] }}"></td>
                                        @endif
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
