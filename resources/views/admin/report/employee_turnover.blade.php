@extends('layout.default')
@section('title') Admin | Báo cáo doanh thu nhân viên @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Báo cáo doanh thu nhân viên'),
'content' => [
__('Báo cáo doanh thu nhân viên') => ''
],
'active' => [__('Báo cáo doanh thu nhân viên')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{ url('theme/admin-lte/plugins/table2excel/jquery.table2excel.js') }}"></script>
    <script src="{{ url("js/reports/common.js") }}"></script>
    <script src="{{asset('js/plugins/jquery.doubleScroll.js')}}"></script>
    <script>
        $('.table-responsive').doubleScroll({
            resetOnWindowResize:true
        })
    </script>
  <script>
    $(function() {
				$("#exportToExcel").click(function(e){
					var table = $('#ReportTable');
					if(table && table.length){
						var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
						$(table).table2excel({
							exclude: ".noExl",
							name: "Báo cáo doanh thu nhân viên",
							filename: "employee_turnover_" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls",
							fileext: ".xls",
							exclude_img: true,
							exclude_links: true,
							exclude_inputs: true,
							preserveColors: preserveColors
						});
					}
				});

			});
  </script>
  <style>
    .table td, .table th {
      vertical-align: middle;
    }
    @media print{ #responsive_tb{ height:100%;width: 100%; overflow:visible;} }
  </style>
@stop

@section('content')
<iframe id="txtArea" class="d-none"></iframe>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <!-- /.modal -->
                        <div class="text-center" style="float: right">
                            <button class="btn btn-primary" onclick="$('form#search').submit()">Xem báo cáo</button>
                            <button class="btn btn-default" id="print"><i class="fas fa-print"></i> In</button>
                            <button class="btn btn-success" id="exportToExcel">Excel</button>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="print_content">
                        <form action="{{route('admin.report.employee_turnover')}}" method="post" id="search">
                            @csrf
                            <div class="row">
                                <div class="form-group col">
                                  <input name="create_date_from" id="create_date_from" class="form-control  datepicker" type="text"
                                    value="{{date('d/m/Y',strtotime($conditions['create_date_from']))}}">
                                </div>
                                <div class="form-group col">
                                  <input name="create_date_to" id="create_date_to" class="form-control  datepicker" type="text"
                                  value="{{date('d/m/Y',strtotime($conditions['create_date_to']))}}">
                                </div>
                                <div class="form-group col">
                                    <select name="source_id"  class="form-control ">
                                        <option value="">Đơn nguồn từ(tất cả)</option>
                                        @foreach ($orderSources as $key => $item)
                                          <option value="{{$key}}"
                                          {{isset($conditions['source_id']) && $conditions['source_id'] == $key ? 'selected' : ''}}>{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col">
                                  <select name="upsale_from_user_id"  class="form-control ">
                                    <option value="">Nguồn Up Sale(tất cả)</option>
                                    @foreach ($usersUpSale as $key => $item)
                                      <option value="{{$key}}"
                                      {{isset($conditions['upsale_from_user_id']) && $conditions['upsale_from_user_id'] == $key ? 'selected' : ''}}>{{$item}}</option>
                                    @endforeach
                                  </select>
                                </div>
                                <div class="form-group col">
                                  <select name="type"  class="form-control ">
                                    <option value="">Tất cả đơn</option>
                                    @foreach ($orderType as $key => $item)
                                      <option value="{{$key}}"
                                      {{isset($conditions['type']) && $conditions['type'] == $key ? 'selected' : ''}}>{{$item}}</option>
                                    @endforeach
                                  </select>
                                </div>
                                <div class="form-group col">
                                  <select name="user_type"  class="form-control ">
                                    <option value="{{ ACTIVE }}"
                                      {{isset($conditions['user_type']) && $conditions['user_type'] == ACTIVE ? 'selected' : ''}}>Tài khoản kích hoạt</option>
                                    <option value="{{ INACTIVE }}"
                                    {{isset($conditions['user_type']) && $conditions['user_type'] == INACTIVE ? 'selected' : ''}}>Tài khoản chưa kích hoạt</option>
                                    <option value="">Tất cả</option>
                                  </select>
                                </div>
                                <div class="form-group col">
                                  <select name="type_date" class="form-control">
                                    <option value="created_at" {{ array_get($conditions,'type_date') === 'created_at' ? 'selected' : '' }}>Ngày tạo đơn</option>
                                    <option value="close_date" {{ array_get($conditions,'type_date') === 'close_date' ? 'selected' : '' }}>Ngày chốt đơn</option>
                                  </select>
                                </div>
                                <div class="form-group col">
                                  <select name="user_groups"  class="form-control">
                                    <option value="">Tất cả các nhóm tài khoản</option>
                                    @foreach ($user_groups as $key => $item)
                                      <option value="{{$key}}"
                                      {{isset($conditions['user_groups']) && $conditions['user_groups'] == $key ? 'selected' : ''}}>{{$item}}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                        </form>
                        <hr class="hr-form">
                        <div class="row">
                          <div class="col-md-2">
                            <ul class="list-unstyled">
                              <li>{{$userLogin->name}}</li>
                              <li>Điện thoại: {{$userLogin->phone}}</li>
                              <li>Địa chỉ: {{$userLogin->address}}</li>
                            </ul>
                          </div>
                          <div class="col-md-8 text-center">
                            <h2>BÁO CÁO DOANH THU NHÂN VIÊN</h2>
                            <span>(Chỉ dành cho nhân viên sale)</span><br>
                            <span>Ngày {{date('d/m/Y', strtotime($conditions['create_date_from']))}} đến {{date('d/m/Y',strtotime($conditions['create_date_to']))}}</span>
                          </div>
                          <div class="col-md-2 text-right">
                            <ul class="list-unstyled">
                              <li>Ngày in: {{date('d/m/Y')}}</li>
                              <li>Tài khoản in: {{$userLogin->account_id}}</li>
                            </ul>
                          </div>
                        </div>
                        <div class="table-responsive" id="responsive_tb">
                          <table id="ReportTable" width="100%" class="table table-bordered">
                            <thead>
                                <tr style="font-weight:bold">
                                    <th rowspan="2" class="text-center">Nhân viên</th>
                                    <th colspan="2" class="text-center">Xác Nhận - Chốt đơn</th>
                                    <th colspan="2" class="text-center">Hủy</th>
                                    <th colspan="2" class="text-center">Kế toán mặc định</th>
                                    <th colspan="2" class="text-center">Chuyển hàng</th>
                                    <th colspan="2" class="text-center">Chuyển hoàn</th>
                                    <th colspan="2" class="text-center">Thành công</th>
                                    <th colspan="2" class="text-center">Đã thu tiền</th>
                                    {{-- <th colspan="2" class="text-center">Khác</th> --}}

                                    <th colspan="2" class="text-center">Tổng</th>
                                    <th nowrap="" style="background:#dd4649;color:#fff;">% hoàn</th>
                                    <th nowrap="" style="background:#9bdd99;">% chốt</th>
                                    <th nowrap="" style="background:#9bdd99;">% th.công</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th>
                                    {{-- <th class="text-center">Số Lượng</th>
                                    <th class="text-center">Doanh thu</th> --}}
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="body_table">
                                @php
                                  $totalCloseDataCount = 0;
                                  $totalCloseDataPrice = 0;

                                  $totalCancelCount = 0;
                                  $totalCancelPrice = 0;

                                  $totalAccountingDefaultCount = 0;
                                  $totalAccountingDefaultPrice = 0;

                                  $totalShippingCount = 0;
                                  $totalShippingPrice = 0;

                                  $totalTransferredBackCount = 0;
                                  $totalTransferredBackPrice = 0;

                                  $totalSuccessCount = 0;
                                  $totalSuccessPrice = 0;

                                  $totalCollectedMoneyCount = 0;
                                  $totalCollectedMoneyPrice = 0;
                                  $allCount = 0;
                                  $allPrice = 0;
                                @endphp

                                @foreach ($data['close_date'] as $key => $user)
                                  @php
                                    if(!$user) { continue; };
                                      $totalOrderUser = $data['total'][$key]['order_total_user'];
                                      $returnPercent= 0;
                                      $successPercent = 0;
                                      $totalCloseOrderUser = 0;

                                      // % trả hàng
                                      $returnPercent = $totalOrderUser > 0 ? round( ($user['count_6'] + $user['count_11']) / $totalOrderUser * 100, 2) : 0;

                                      // % thành công
                                      $successPercent = $totalOrderUser > 0 ? round(($user['count_7'] + $user['count_10']) / $totalOrderUser * 100, 2) : 0;

                                      // % chốt
                                      foreach (STATUS_DON_HANG_CHOT as $key => $value) {
                                        $item = 'count_'.$value;
                                        $totalCloseOrderUser +=  $user[$item];
                                      }
                                      $closePercent = $totalOrderUser > 0 ? round( $totalCloseOrderUser / $totalOrderUser * 100, 2) : 0;

                                      $totalCloseDataCount += $user['count_5'];
                                      $totalCloseDataPrice += $user['sum_5'];

                                      $totalCancelCount += $user['count_3'];
                                      $totalCancelPrice += $user['sum_3'];

                                      $totalAccountingDefaultCount += $user['count_9'];
                                      $totalAccountingDefaultPrice += $user['sum_9'];

                                      $totalShippingCount += $user['count_4'];
                                      $totalShippingPrice += $user['sum_4'];

                                      $totalTransferredBackCount += $user['count_6'];
                                      $totalTransferredBackPrice += $user['sum_6'];

                                      $totalSuccessCount += $user['count_7'];
                                      $totalSuccessPrice += $user['sum_7'];

                                      $totalCollectedMoneyCount += $user['count_10'];
                                      $totalCollectedMoneyPrice += $user['sum_10'];
                                      $allCount += $user['count_total'];
                                      $allPrice += $user['sum_total'];

                                      // $countDiff = $user['count_total'] - $user['count_5'] - $user['count_3'] - $user['count_9'] - $user['count_4'] - $user['count_6'] - $user['count_7'] - $user['count_10'];
                                      // $sumDiff = $user['sum_total'] - $user['sum_5'] - $user['sum_3'] - $user['sum_9'] - $user['sum_4'] - $user['sum_6'] - $user['sum_7'] - $user['sum_10'];
                                  @endphp
                                  <tr data-value="{{ $user['sum_total'] }}" data-index="{{ $loop->index }}">
                                    <td>{{$user->user_name}}
                                        <div class="small" style="color:#999;font-style: italic;"> {{$user->account_id}} </div>
                                    </td>
                                    <td class=" text-center">{{ number_format($user['count_5']) }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_5'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['count_3'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_3'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['count_9'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_9'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['count_4'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_4'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['count_6'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_6'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['count_7'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_7'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['count_10'])  }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_10'])  }}</td>
                                    {{-- <td class=" text-center">{{ number_format($countDiff)  }}</td> --}}
                                    {{-- <td class=" text-center">{{ number_format($sumDiff)  }}</td> --}}
                                    <td class=" text-center">{{ number_format($user['count_total']) }}</td>
                                    <td class=" text-center">{{ number_format($user['sum_total']) }}</td>


                                    <td class="text-center">{{ isset($returnPercent) ? $returnPercent.'%' : 0}}</td>
                                    <td class="text-center">{{ isset($closePercent) ? $closePercent.'%' : 0}}</td>
                                    <td class="text-center">{{ isset($successPercent) ? $successPercent.'%' : 0}}</td>

                                  </tr>
                                @endforeach
                                  {{-- @php
                                      $totalDiffCount = $allCount - $totalCloseDataCount - $totalCancelCount - $totalAccountingDefaultCount - $totalShippingCount - $totalTransferredBackCount -$totalSuccessCount - $totalCollectedMoneyCount;
                                      $totalDiffPrice = $allPrice - $totalCloseDataPrice - $totalCancelPrice - $totalAccountingDefaultPrice - $totalShippingPrice - $totalTransferredBackPrice -$totalSuccessPrice - $totalCollectedMoneyPrice;
                                  @endphp --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Tổng</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalCloseDataCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalCloseDataPrice)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalCancelCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalCancelPrice)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalAccountingDefaultCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalAccountingDefaultPrice)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalShippingCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalShippingPrice)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalTransferredBackCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalTransferredBackPrice)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalSuccessCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalSuccessPrice)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalCollectedMoneyCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($totalCollectedMoneyPrice)}}</strong></td>
                                    {{-- <td class=" text-center"><strong>{{number_format($totalDiffCount)}}</strong></td> --}}
                                    {{-- <td class=" text-center"><strong>{{number_format($totalDiffPrice)}}</strong></td> --}}
                                    <td class=" text-center"><strong>{{number_format($allCount)}}</strong></td>
                                    <td class=" text-center"><strong>{{number_format($allPrice)}}</strong></td>
                                    <td class="text-center">x</td>
                                    <td class="text-center">x</td>
                                    <td class="text-center">x</td>
                                </tr>
                            </tbody>
                          </table>
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
@section('scripts')
<script>
    var $wrapper = $('#body_table');
    $wrapper.find('tr[data-value]').sort(function(a, b) {
        return b.dataset.value - a.dataset.value;
    })
    .appendTo($wrapper);
</script>
@endsection
