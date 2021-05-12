
@extends('layout.default')
<?php
    $month = request('month') != null ? request('month') : date('m');
    $name = 'BIỂU ĐỒ THỐNG KÊ TRẠNG THÁI ĐƠN HÀNG THÁNG '.$month;
?>
@section('title') Admin | BIỂU ĐỒ THỐNG KÊ TRẠNG THÁI ĐƠN HÀNG THÁNG {{$name}} @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __($name),
'content' => [
__($name) => ''
],
'active' => [__($name)]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{url('theme/admin-lte/plugins/highcharts/highcharts.js')}}"></script>
    <script src="{{url('theme/admin-lte/plugins/highcharts/data.js')}}"></script>
    <script src="{{url('theme/admin-lte/plugins/highcharts/exporting.js')}}"></script>
    <script src="{{asset('js/reports/common.js')}}"></script>

    <script type="text/javascript">
        var data = JSON.parse('<?php echo json_encode($data) ?>');
        var result = result =  Object.keys(data).map(function(key) {
            return data[key];
        });
        pieChart('chartContainer',result,'{{$name}}','Số đơn')
    </script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <form method="get" id="frm">
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Tháng</span>
                                                </div>
                                                <select name="month" id="month" class="form-control"
                                                        aria-describedby="sizing-addon2"
                                                        onchange="search()">
                                                    <option value="">Chọn tháng</option>
                                                        @if( !empty(MONTH_NUMBER))
                                                            @foreach(MONTH_NUMBER as $item)
                                                                <option value="{{$item}}" @if($month == $item) selected @endif>Tháng {{$item}}</option>
                                                            @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Năm</span>
                                                </div>
                                                <select name="year" id="year" class="form-control"
                                                        aria-describedby="sizing-addon2"
                                                        onchange="search()">
                                                    <?php
                                                        $now = date('Y');
                                                        $from = $now - 3;
                                                        $to = $now + 9;
                                                        $year = request('year') != null ? request('year') : $now;
                                                    ?>
                                                    @for($i = $from;$i <= $to;$i++)
                                                        <option value="{{$i}}" @if( $year == $i) selected @endif>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="reportForm">
                      <div class="row">

                          <div class="col-md-12">
                              <div id="chartContainer"></div>
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
@stop
