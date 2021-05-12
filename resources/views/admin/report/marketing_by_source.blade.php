@extends('layout.default')
@php
    $columns = \App\Models\Order::getMktSourceIndex();
@endphp
@section('assets')
    <script src="{{url('theme/admin-lte/plugins/highcharts/highcharts.js')}}"></script>
    <script src="{{url('theme/admin-lte/plugins/highcharts/data.js')}}"></script>
    <script src="{{url('theme/admin-lte/plugins/highcharts/exporting.js')}}"></script>
    <script src="{{asset('js/reports/common.js')}}"></script>
    <script src="{{asset('js/kendo.ui.core.min.js')}}"></script>
    <script>
        var categories = JSON.parse('<?php echo json_encode(array_keys($data['chart'])) ?>');
        var data = JSON.parse('<?php echo json_encode(array_values($data['chart'])) ?>');
        Highcharts.chart('chartNguon', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Biểu đồ báo cáo Marketing theo nguồn'
            },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Doanh thu (VNĐ)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:,.0f} VNĐ</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{"name":"Doanh thu","data":data}]});

        var categoriesTyLeChot = JSON.parse('<?php echo json_encode(array_keys($tyLeChotMkt)) ?>');
        var dataTyLeChot = JSON.parse('<?php echo json_encode(array_values($tyLeChotMkt)) ?>');
        Highcharts.chart('chartTyLeChot', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Biểu đồ báo cáo tỷ lệ chốt của Marketing{{ !empty($tyLeCuaMkt) ? ": ".$tyLeCuaMkt->name." (Từ ".$params['from']." tới ".$params['to'].")" : "(Tất cả)" }}'
            },
            xAxis: {
                categories: categoriesTyLeChot,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Tỷ lệ (%)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:,.2f} %</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{"name":"Tỷ lệ chốt","data":dataTyLeChot}]});

        function updateTable(){
            var percent = {
                cpl: [],
            };
            $('tr[data-key]').each(function(){
                var key = this.dataset.key;
                var filtered = window.data.filter(x => x.source_id == key);
                var orders = filtered.filter(x => [4,5,7,9,10].includes(x.status_id));
                var lead = filtered.length;
                var cost = formatToNumeric($(this).find('.js-cost input').val());
                var cpl = lead ? Math.round(cost/lead) : 0;
                var orders_total = orders.map(x => x.total_price);
                orders_total.push(0);
                var revenue = orders_total.reduce((a,b)=>a+b);
                var rate_of_return = revenue ? (cost/revenue*100).toFixed(2) : 0;

                percent.cpl.push(cpl*1);

                $(this).find(`[data-key="lead"]`).text(formatNumber(lead));
                $(this).find(`[data-key="cost_per_lead"]`).text(!lead ? 'N/A' : formatNumber(cpl));
                $(this).find(`[data-key="orders"]`).text(formatNumber(filtered.filter(x => [4,5,7,9,10].includes(x.status_id)).length));
                $(this).find(`[data-key="revenue"]`).text(formatNumber(revenue));
                $(this).find(`[data-key="close_rate"]`).text(lead?(orders.length/lead*100).toFixed(2):'N/A');
                $(this).find(`[data-key="rate_of_return"]`).text(revenue?rate_of_return:'N/A');


                $(`[data-layout="total"] [data-key="lead"]`).text(formatNumber(formatToNumeric($(`[data-layout="total"] [data-key="lead"]`).text()) + lead));
                $(`[data-layout="total"] [data-key="orders"]`).text(formatNumber(formatToNumeric($(`[data-layout="total"] [data-key="orders"]`).text()) + orders.length));
            });
            var revenue = window.data.filter(x => [4,5,7,9,10].includes(x.status_id)).length ? window.data.filter(x => [4,5,7,9,10].includes(x.status_id)).map(x => x.total_price).reduce((a,b)=>a+b) : 0;
            var total_cost = $('[name="cost[]"]').map(function(){return formatToNumeric(this.value);}).get().reduce((a,b)=>a+b);
            $(`[data-layout="total"] [data-key="cost"]`).text(formatNumber(total_cost));
            $(`[data-layout="total"] [data-key="cost_per_lead"]`).text(formatNumber(percent.cpl.filter(x=>x>0).length?(percent.cpl.reduce((a,b)=>a+b)/percent.cpl.filter(x=>x>0).length).toFixed(0):0));
            $(`[data-layout="total"] [data-key="close_rate"]`).text((formatToNumeric($(`[data-layout="total"] [data-key="orders"]`).text())/formatToNumeric($(`[data-layout="total"] [data-key="lead"]`).text())*100).toFixed(2));
            $(`[data-layout="total"] [data-key="rate_of_return"]`).text(revenue?(total_cost/revenue*100).toFixed(2):0);

        }
        function loadReport(){
            $.ajax({
                url:"/admin/report/marketing-by-source",
                method:'POST',
                data:{
                    from        : $('[name="date_begin"]').val(),
                    to          : $('[name="date_end"]').val(),
                    marketing_id: $('[name="marketing_id"]').val(),
                    active_account: $('[name="active_account"]').val(),
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
                    loadReport();
                }
            })
        }
        function updateInitCost(elm){
            $.ajax({
                url:'/admin/report/update-init-cost',method:'POST',dataType:'json',
                data:{marketing_id:$('[name="marketing_id"]').val(),key:elm.dataset.key,value:formatToNumeric(elm.value),date:$('[name="date_begin"]').val()},
                success:function(resp){
                    toastr[resp.success?'success':'error'](resp.msg)
                },error:function(x,h,r){
                    toastr['error'](x.responseJSON&&x.responseJSON.message?x.responseJSON.message:r);
                }
            })
        }
        function updateMktCost(elm){
            $.ajax({
                url:'/admin/report/update-mkt-cost',method:'POST',dataType:'json',
                data:{key:elm.dataset.key,value:formatToNumeric(elm.value),date:$('[name="date_begin"]').val()},
                success:function(resp){
                    toastr[resp.success?'success':'error'](resp.msg)
                },error:function(x,h,r){
                    toastr['error'](x.responseJSON&&x.responseJSON.message?x.responseJSON.message:r);
                }
            })
        }
        function getMktCost(){
            $.ajax({
                url:'/admin/report/get-mkt-cost',method:'POST',dataType:'json',data:{
                    begin:$('[name="date_begin"]').val(),end:$('[name="date_end"]').val(),
                    marketer_id:$('[name="marketing_id"]').val(),
                    active_account:$('[name="active_account"]').val(),
                },
                success:function(resp){
                    if (resp.success) {
                        if (resp.cost) {
                            $('.js-total-cost').text('')
                            Object.keys(resp.cost).forEach(source_id => {
                                var data = resp.cost[source_id];
                                if (data) {
                                    Object.keys(data).forEach(date => {
                                        if (data[date]) {
                                            var old = formatToNumeric($('.js-total-cost[data-key="'+date+'"]').text());
                                            var new_val = old + parseInt(formatToNumeric(data[date]));
                                            $('.js-total-cost[data-key="'+date+'"]').text(formatNumber(new_val));
                                        }
                                    });
                                    var cost_value = resp.cost_data[source_id] ? resp.cost_data[source_id].toFixed(0)*1 : 0;
                                    $('[data-key="'+source_id+'"] [data-key="cost"] input').val(formatNumber(cost_value));
                                }
                                var cost_items = $('[data-key] .js-cost input').map(function(){return formatToNumeric(this.value);}).get();
                                $('[data-layout="total"] [data-key="cost"]').text(formatNumber(cost_items.length ? cost_items.reduce((a,b) => a+b) : 0));
                            });
                        }
                        if (resp.mkt_costs) {
                            $('.js-total-init-cost').text('')
                            for(var i=0;i < resp.mkt_costs.length;i++){
                                var user_id = resp.mkt_costs[i].user_id;
                                if (user_id == $('[name="marketing_id"]').val() || $('[name="marketing_id"]').val() === '0') {
                                    var amount = resp.mkt_costs[i].amount;
                                    var date_fmt = new Date(resp.mkt_costs[i].day).toLocaleDateString().split('/').map(x => x.padStart(2, '0')).join('/');
                                    var old_val = formatToNumeric($(`.js-total-init-cost[data-key="${date_fmt}"]`).text());
                                    console.log(old_val,amount);
                                    $(`.js-total-init-cost[data-key="${date_fmt}"]`).text(formatNumber(old_val + amount));
                                }
                            }
                        }
                        $('[data-layout="total"] [data-key="init_cost"]').text('')
                        $('[data-key="init_cost"] input').val('')
                        $('#ReportTable tbody tr[data-key]').each(function(){
                            var key = this.dataset.key;
                            var init = resp.init_cost[key]||0;
                            $(this).find('[data-key="init_cost"] input').val(formatNumber(init));
                            //Cộng tổng cột Cấp ADS
                            $('[data-layout="total"] [data-key="init_cost"]').text(formatNumber(
                                formatToNumeric($('[data-layout="total"] [data-key="init_cost"]').text()) + init
                            ));
                        });
                        $('#ReportTable tbody tr[data-key]').each(function(){
                            var key = this.dataset.key;
                            var value = formatToNumeric($(this).find('[data-key="cost"] input').val());
                            var value_sub = formatToNumeric($(this).find('[data-key="init_cost"] input').val());
                            $(this).find('[data-key="cost_remain"] input').val(formatNumber(value_sub-value));
                        });
                        //Tính tổng cột Còn lại
                        var cost_items = $('[data-key="cost_remain"] input').map(function(){return formatToNumeric(this.value);}).get();
                        $('[data-layout="total"] [data-key="cost_remain"]').text(formatNumber(cost_items.length ? cost_items.reduce((a,b) => a+b) : 0));
                    } else {
                        toastr['error'](resp.msg);
                    }
                },error:function(x,h,r){
                    toastr['error'](x.responseJSON&&x.responseJSON.message?x.responseJSON.message:r);
                }
            })
        }
        $(function(){
            getMktCost();
            $('#source_id').on('change',function(){
                getMktCost();
            });
            $('[name="marketing_id"]').on('change',function(){
                getMktCost();
            });
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
                                <h3 class="alert alert-info">Báo cáo Marketing theo nguồn (tính theo ngày tạo đơn) <br><small class="text-sm">Lưu ý: Các bạn marketer vui lòng nhập chi phí ADS của 3 ngày gần nhất (không tính ngày hôm nay) trong thời gian từ 0h đến 12h00.</small></h3>
                            </div>
                        </div>
                        <form action="">
                            <div class="row">
                                <div class="col-md-2">
                                    <input onchange="getMktCost()" value="{{ array_get($filters,'date_begin', date('01/m/Y')) }}" name="date_begin" class="form-control datepicker">
                                </div>
                                <div class="col-md-2">
                                    <input onchange="getMktCost()" value="{{ array_get($filters,'date_end', date('d/m/Y')) }}" name="date_end" class="form-control datepicker">
                                </div>
                                <div class="col-md-2">
                                    <select name="active_account" class="form-control select2">
                                        <option value="0">- Tài khoản kích hoạt -</option>
                                        <option {{ request()->input('active_account') == 1 ? 'selected' : '' }} value="1">Có</option>
                                        <option {{ request()->input('active_account') == 2 ? 'selected' : '' }} value="2">Không</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="marketing_id" class="form-control select2">
                                        <option value="0">-Chọn marketer-</option>
                                        @foreach($marketers as $marketer)
                                        @if (!request()->input('active_account') || (request()->input('active_account') && request()->input('active_account') == 1 && $marketer->status) || (request()->input('active_account') && request()->input('active_account') == 2 && !$marketer->status))
                                        <option data-active="{{ $marketer->status }}" {{ $marketer->id == request()->input('marketing_id') ? 'selected' : '' }} value="{{ $marketer->id }}">{{ $marketer->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    <button id="btnExport" type="button" onclick="fnExcelReport('ReportTable');" class="btn btn-info"><i class="fa fa-download"></i> Xuất Excel </button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-md-8">
                                <div id="html">
                                    <iframe id="txtArea1" style="display:none"></iframe>
                                    <table id="ReportTable" width="100%" class="table table-bordered table-sm" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Chi phí ADS</th>
                                                <th>Cấp ADS</th>
                                                <th>Còn lại</th>
                                                @foreach($columns as $column)
                                                <th class="text-center">{{ $column['text'] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sources as $source_id => $source)
                                            <tr data-key="{{ $source_id }}">
                                                <th>{{ $source }}</th>
                                                <th class="js-cost" data-key="cost"><input class="text-right form-control js-currency" name="cost[]" data-key="{{ $source_id }}" onchange="updateMktCost(this)"></th>
                                                <th class="js-init-cost" data-key="init_cost"><input class="text-right form-control js-currency" name="init_cost[]" data-key="{{ $source_id }}" onchange="updateInitCost(this)"></th>
                                                <th class="js-cost-remain" data-key="cost_remain"><input class="text-right form-control js-currency js-cost-remain" data-key="{{ $source_id }}" readonly></th>
                                                @foreach($columns as $column)
                                                @if(array_get($column,'type')=='input')
                                                <td class="text-center" data-key="{{ $column['code'] }}" class="js-{{ $column['code'] }}">
                                                    <input name="{{ $column['code'] }}[]" class="form-control text-right {{ $column['code']=='labor_day'? '': 'js-currency' }}" onchange="update(this)">
                                                </td>
                                                @else
                                                <td class="text-center" data-key="{{ $column['code'] }}"></td>
                                                @endif
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr data-layout="total">
                                                <th>Tổng</th>
                                                <th class="text-center" data-key="cost"></th>
                                                <th class="text-center" data-key="init_cost"></th>
                                                <th class="text-center" data-key="cost_remain"></th>
                                                @foreach($columns as $column)
                                                @if(array_get($column,'type')=='input')
                                                <td class="text-center" data-key="{{ $column['code'] }}" class="js-{{ $column['code'] }}"></td>
                                                @else
                                                <td class="text-center" data-key="{{ $column['code'] }}"></td>
                                                @endif
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ngày</th>
                                                <th>Chi phí AD (VNĐ)</th>
                                                <th>Cấp ADS (VNĐ)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(range(0,14) as $key)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>
                                                    {{ date('d/m/Y',strtotime(' - '.$key.' days')) }}
                                                </td>
                                                <td data-key="{{ date('d/m/Y',strtotime(' - '.$key.' days')) }}" class="js-total-cost">

                                                </td>
                                                <td data-key="{{ date('d/m/Y',strtotime(' - '.$key.' days')) }}" class="js-total-init-cost">

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ngày</th>
                                                <th>Chi phí AD (VNĐ)</th>
                                                <th>Cấp ADS (VNĐ)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(range(15,30) as $key)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>
                                                    {{ date('d/m/Y',strtotime(' - '.$key.' days')) }}
                                                </td>
                                                <td data-key="{{ date('d/m/Y',strtotime(' - '.$key.' days')) }}" class="js-total-cost">

                                                </td>
                                                <td data-key="{{ date('d/m/Y',strtotime(' - '.$key.' days')) }}" class="js-total-init-cost">

                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="chartNguon" style="min-width: 310px; min-height: 400px; margin: 0px auto; overflow: hidden; width: auto" data-highcharts-chart="0"></div>
                            </div>
                            <div class="col-md-12">
                                <div id="chartTyLeChot" style="min-width: 310px; min-height: 400px; margin: 0px auto; overflow: hidden; width: auto" data-highcharts-chart="1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
