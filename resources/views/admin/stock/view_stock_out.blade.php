@extends('layout.default')
@section('title') Admin | Xem phiếu xuất kho @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    
      @if($type == STOCK_OUT)
        @php
          $breadcrumb = [
              'title' => __('Xem phiếu xuất kho'),
              'content' => [
                  __('Xem phiếu xuất kho') => route('admin.stock.stock_out.view')
              ],
              'active' => [__('Xem phiếu xuất kho')]
          ];
        @endphp
      @else
        @php
        $breadcrumb = [
            'title' => __('Xem phiếu nhập kho'),
            'content' => [
                __('Xem phiếu nhập kho') => route('admin.stock.stock_in.view')
            ],
            'active' => [__('Xem phiếu nhập kho')]
        ];
        @endphp
      @endif
    
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script>
        $('#print').click(function() {
            window.print();
        });
        (function() {
          var beforePrint = function() {
            $('.card-header').addClass('d-none')
            $('footer').addClass('d-none')
          };

          var afterPrint = function() {
            $('.card-header').removeClass('d-none')
            $('footer').removeClass('d-none')
          };

          if (window.matchMedia) {
              var mediaQueryList = window.matchMedia('print');
              mediaQueryList.addListener(function(mql) {
                  if (mql.matches) {
                      beforePrint();
                  } else {
                      afterPrint();
                  }
              });
          }

          window.onbeforeprint = beforePrint;
          window.onafterprint = afterPrint;

        }());
    </script> 
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- /.modal -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="text-right">
                                        <a href="{{$type == STOCK_IN ? route('admin.stock.stock_in_list') : route('admin.stock.stock_out_list')}}" class="btn btn-primary"> Danh sách phiếu</a>
                                        <button id="print" class="btn btn-success"><i class="fas fa-print"></i> In</button>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <ul class="list-unstyled">
                                <li>Đơn vị: {{ isset($shop->name) ? $shop->name : ''}}</li>
                                <li>Điện thoại: {{ isset($shop->phone) ? $shop->phone : ''}}</li>
                                <li>Địa chỉ: {{ isset($shop->address) ? $shop->address : ''}}</li>
                              </ul>
                            </div>
                            <div class="col-md-6 text-right">
                              <ul class="list-unstyled">
                                <li>Số: {{$entity->bill_number}}</li>
                                <li>Ngày: {{date('d/m/Y',strtotime(now()))}}</li>
                              </ul>
                            </div>
                          </div>
                          <div class="row text-center">
                          <h3 class="text-center w-100">{{$type == STOCK_IN ? 'PHIẾU NHẬP KHO' : 'PHIẾU XUẤT KHO'}}</h3>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <ul class="list-unstyled">
                                <li>Người giao: {{$entity->deliver_name}}</li>
                                <li>Người nhận: {{$entity->receiver_name}}</li>
                                <li>Diễn giải: {{$entity->note}}</li>
                              </ul>
                            </div>
                            <div class="col-md-6 text-right">
                              <ul class="list-unstyled">
                                <li>Nhân viên: {{isset($user->name) ? $user->name : ''}}</li>
                              </ul>
                            </div>
                          </div>
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th class="text-center">STT</th>
                                <th class="text-center">Tên SP, HH</th>
                                <th class="text-center">Mã</th>
                                <th class="text-center">Đơn vị</th>
                                <th class="text-center">Kho</th>
                                <th class="text-center">Số lượng</th>
              
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="text-center">A</td>
                                <td class="text-center">B</td>
                                <td class="text-center">C</td>
                                <td class="text-center">D</td>
                                <td class="text-center">E</td>
                                <td class="text-center">F</td>
                               
                              </tr>
                              @foreach ($stockProducts as $key => $item)
                                <tr>
                                  <td class="text-center">{{ $key +1 }}</td>
                                  <td class="text-center">{{isset($item->product->name)?$item->product->name:''}}</td>
                                  <td class="text-center">{{ isset($item->product->code) ? $item->product->code :'' }}</td>
                                  <td class="text-center">{{ $item->unit_name }}</td>
                                  <td class="text-center">{{ isset($item->stockGroup->name) ? $item->stockGroup->name :'' }}</td>
                                  <td class="text-center">{{ $item->quantity }}</td>
                                  
                                </tr>
                              @endforeach
                              @for ($i = 0; $i <= 10; $i++)
                                <tr>
                                  <td class="text-center"></td>
                                  <td class="text-center"></td>
                                  <td class="text-center"></td>
                                  <td class="text-center"></td>
                                  <td class="text-center"></td>
                                  <td class="text-center"></td>
                                  
                                </tr>
                              @endfor
                              <tr>
                                <td class="text-center"></td>
                                <td class="text-center"><strong>Tổng cộng</strong></td>
                                <td class="text-center">x</td>
                                <td class="text-center">x</td>
                                <td class="text-center">x</td>
                                <td class="text-center">x</td>
                               
                              </tr>
                            </tbody>
                          </table>
                          <div class="row">
                            <h5 class="w-100 text-right">Ngày {{date('d',strtotime(now()))}} tháng {{date('m',strtotime(now()))}} năm {{date('Y',strtotime(now()))}}</h5>
                          </div>
                          <div class="row">
                            <div class="col-md-3 text-center">
                              <span>Thủ trưởng đơn vị</span><br>
                              <span>(Ký, họ tên)</span>
                            </div>
                            <div class="col-md-3 text-center">
                              <span>Thủ trưởng đơn vị</span><br>
                              <span>(Ký, họ tên)</span>
                            </div>
                            <div class="col-md-3 text-center">
                              <span>Thủ trưởng đơn vị</span><br>
                                <span>(Ký, họ tên)</span>
                            </div>
                            <div class="col-md-3 text-center">
                              <span>Thủ trưởng đơn vị</span><br>
                                <span>(Ký, họ tên)</span>
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
