
@extends('layout.default')
@section('title') Admin | Báo Cáo doanh số chốt sale theo data MKT (Triệu VND) @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Báo Cáo doanh số chốt sale theo data MKT (Triệu VND)'),
            'content' => [
                __('Báo Cáo doanh số chốt sale theo data MKT (Triệu VND)') => ''
            ],
            'active' => [
                __('Báo Cáo doanh số chốt sale theo data MKT (Triệu VND)')
            ]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script id="tblSaleMktAlignmentTpl" type="text/x-kendo-template">
        <div >
            <table border="1px" class="table table-xs table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        #= Object.keys(marketers).map(x => '<th><span><b>'+marketers[x]+'</b></span></th>').join('') #
                        <th>Hotline</th>
                        <th>Khách cũ</th>
                    </tr>
                </thead>
                <tbody>
                # for(var i=0;i<Object.keys(sales).length;i++){ #
                    <tr>
                        <th><b>#= sales[Object.keys(sales)[i]] #</b></th>
                        #= Object.keys(marketers).map(x => '<td class="text-right"><span class="text-muted" data-marketer_id="'+x+'" data-sale_id="'+Object.keys(sales)[i]+'">0</span></td>').join('') #
                        <td class="text-right"><span data-split="hotline" data-sale_id="#= Object.keys(sales)[i] #">0</span></td>
                        <td class="text-right"><span data-split="old" data-sale_id="#= Object.keys(sales)[i] #">0</span></td>
                    </tr>
                # } #
                    <tr class="">
                        <th><b>Khách mới</b></th>
                        #= Object.keys(marketers).map(x => '<td class="text-right"><span data-layout="new" data-key="'+x+'">0</span></td>').join('') #
                        <td class="text-right"><span>0</span></td>
                        <td class="text-right"><span>0</span></td>
                    </tr>
                    <tr class="">
                        <th><b>Khách cũ</b></th>
                        #= Object.keys(marketers).map(x => '<td class="text-right"><span data-layout="old" data-key="'+x+'">0</span></td>').join('') #
                        <td class="text-right"><span>0</span></td>
                        <td class="text-right"><span>0</span></td>
                    </tr>
                    <tr class="">
                        <th><b>TỔNG</b></th>
                        #= Object.keys(marketers).map(x => '<td class="text-right"><span data-total="marketer" data-key="'+x+'">0</span></td>').join('') #
                        <td class="text-right"><span>0</span></td>
                        <td class="text-right"><span>0</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </script>
    <style>

        #html {
            max-width: 100%;
            max-height: 550px;
            overflow: scroll;
        }
        #html table td,#html table th {
            white-space: nowrap;
        }

/* Use position: sticky to have it stick to the edge
 * and top, right, or left to choose which edge to stick to: */

thead th {
  position: -webkit-sticky; /* for Safari */
  position: sticky;
  top: 0;
}

tbody th {
  position: -webkit-sticky; /* for Safari */
  position: sticky;
  left: 0;
}


/* To have the header in the first column stick to the left: */

thead th:first-child {
  left: 0;
  z-index: 2;
}


/* Just to display it nicely: */

thead th {
  background: #000;
  color: #FFF;
  /* Ensure this stays above the emulated border right in tbody th {}: */
  z-index: 1;
}

tbody th {
  background: #FFF;
  border-right: 1px solid #CCC;
  /* Browsers tend to drop borders on sticky elements, so we emulate the border-right using a box-shadow to ensure it stays: */
  box-shadow: 1px 0 0 0 #ccc;
}

table {
  border-collapse: collapse;
}

td,
th {
  padding: 0.5em;
}
    </style>
    <script src="{{asset('js/reports/common.js')}}"></script>
    <script src="{{asset('js/kendo.ui.core.min.js')}}"></script>
    <script>
        $(function(){
            $.ajax({
                url:"{{ route('admin.report.sale-mkt-alignment') }}",
                method:'POST',
                data:{
                    from:$("#from").val(),
                    to:$("#to").val()
                },
                dataType:'json',
                success:function(resp){
                    var template = kendo.template($('#tblSaleMktAlignmentTpl').html());
                    if (resp.success) {
                        $('#html').html(template(resp));
                    }
                    else toastr['error'](resp.msg);
                    $('[data-marketer_id][data-sale_id]').each(function(){
                        var sale_id = this.dataset.sale_id;
                        var marketer_id = this.dataset.marketer_id;
                        var filtered = resp.data.filter(x => (x.assigned_user_id == sale_id || x.user_created == sale_id) && (x.upsale_from_user_id == marketer_id || x.user_created == marketer_id)).map(x => x.total_price);
                        $(this).text(filtered.length ? formatNumber(filtered.reduce((a,b)=>a+b)) : 0);
                    });
                    $('[data-split][data-sale_id]').each(function(){
                        var sale_id = this.dataset.sale_id;
                        var filtered = resp.data.filter(x => ((x.assigned_user_id == sale_id || x.user_created == sale_id) && x.is_old_customer == 1)).map(x => x.total_price);
                        $('[data-split="old"][data-sale_id="'+sale_id+'"]').text(filtered.length ? formatNumber(filtered.reduce((a,b)=>a+b)) : 0);
                        var filtered = resp.data.filter(x => ((x.assigned_user_id == sale_id || x.user_created == sale_id) && x.is_old_customer !== 1)).map(x => x.total_price);
                        $('[data-split="hotline"][data-sale_id="'+sale_id+'"]').text(filtered.length ? formatNumber(filtered.reduce((a,b)=>a+b)) : 0);
                    });
                    $('[data-total="marketer"]').each(function(){
                        var marketer_id = this.dataset.key;
                        var filtered = resp.data.filter(x => (x.upsale_from_user_id == marketer_id || x.user_created == marketer_id) && x.is_old_customer == 1).map(x => x.total_price);
                        $('[data-layout="old"][data-key="'+marketer_id+'"]').text(filtered.length ? formatNumber(filtered.reduce((a,b)=>a+b)) : 0);
                        var filtered = $('[data-marketer_id="'+marketer_id+'"]').map(function(){return formatToNumeric(this.innerText);}).get();
                        $(this).text(filtered.length ? formatNumber(filtered.reduce((a,b)=>a+b)) : 0);
                    });

                    var total = $('[data-total="marketer"]').map(function(){return formatToNumeric(this.innerText);}).get();
                    $('[data-total_amount]').text(formatNumber(total.length ? total.reduce((a,b)=>a+b) : 0));

                    $('[data-total="marketer"]').each(function(){
                        $('[data-layout="new"][data-key="'+this.dataset.key+'"]').text(formatNumber(formatToNumeric($(this).text()) - formatToNumeric($('[data-layout="old"][data-key="'+this.dataset.key+'"]').text())));
                    });

                    $('[data-total],[data-split],[data-layout],[data-total_amount],[data-marketer_id]').each(function(){
                        $(this).text(formatNumber(formatToNumeric($(this).text())/1000000).replace(/,\d+$/gm,''));
                    });

                }
            });
        });
        // $.get( "{{ route('admin.report.get_data_mkt_percent_sale_data') }}?from=" + $("#from").val() + "&to=" + $("#to").val(), function( data ) {
        //     $("#html").html(data);
        //     $("#ReportTable").DataTable();
        // });
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <form method="get">
                                <div class="row">
                                    <div class="col-md-9 d-inline-flex">
                                    <span class="lbl-time">
                                        <strong>
                                            Thời gian:
                                        </strong>
                                    </span>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <input name="from" id="from" class="form-control datepicker" type="text"
                                                       value="{{request('from') != null ? request('from') : date('01/m/Y')}}">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input name="to" id="to" class="form-control datepicker" type="text"
                                                       value="{{request('to') != null ? request('to') : date('d/m/Y')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center col-md-3 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary mr-3">Xem báo cáo</button>
                                        <button type="button" class="btn btn-default" onclick="printDiv('ifrmPrint','reportForm')"><i class="fas fa-print"></i> In</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="reportForm">
                            <div class="row">
                                <div class="col-lg-12 mb-5">
                                    <table width="100%" border="0">
                                        <tbody>
                                        <tr>
                                            <th width="30%" style="text-align: left;">
                                                <div>{!! auth()->user()->name !!}</div>
                                                <div>Điện thoại: {{auth()->user()->phone}}</div>
                                                <div>Địa chỉ: {{auth()->user()->address}}</div>
                                            </th>
                                            <th width="40%" style="text-align: center;">
                                                <h2>Báo Cáo doanh số chốt sale theo data MKT (Triệu VND)</h2>
                                                <div>Ngày {{request('from') != null ? request('from') : date('01/m/Y')}}
                                                    đến {{request('to') != null ? request('to') : date('d/m/Y')}}</div>
                                            </th>
                                            <th width="30%" style="text-align: right;">
                                                <div>Ngày in: {{date('d/m/Y')}}</div>
                                                <div>Tài khoản in: {{auth()->user()->account_id}}</div>
                                            </th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div id="html" style="position:relative;">
                                        Đang tải vui lòng chờ...

                                    </div>
                                </div>
                            </div>
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
    <iframe src="" id="ifrmPrint" class="hidden"></iframe>
@stop
