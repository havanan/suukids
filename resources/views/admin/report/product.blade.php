
@extends('layout.default')
@section('title') Admin | BẢNG KÊ SẢN PHẨM/HÀNG HÓA @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
        'title' => __('BẢNG KÊ SẢN PHẨM/HÀNG HÓA'),
        'content' => [
        __('BẢNG KÊ SẢN PHẨM/HÀNG HÓA') => ''
        ],
        'active' => [__('BẢNG KÊ SẢN PHẨM/HÀNG HÓA')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script src="{{asset('js/reports/common.js')}}"></script>

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
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="text" name="code" class="form-control" placeholder="Mã hàng">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select name="bundle_id" class="form-control">
                                                        <option value="">Tất cả phân loại</option>
                                                        @if(!empty($product_bundles))
                                                            @foreach($product_bundles as $key => $item )
                                                                <option value="{{$item->id}}" @if(isset($params['bundle_id']) && $params['bundle_id'] == $item->id) selected @endif>{{$item->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <input name="from" id="from" class="form-control datepicker" type="text"
                                                               value="{{ $params['from'] }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input name="to" id="to" class="form-control datepicker" type="text"
                                                               value="{{ $params['to'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
{{--                                        Hàng 2--}}
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select name="stock_id" class="form-control">
                                                        <option value="">Tất cả kho</option>
                                                        @if(!empty($stock_groups))
                                                            @foreach($stock_groups as $key => $item )
                                                                <option value="{{$item->id}}" @if(isset($params['stock_id']) && $params['stock_id'] == $item->id) selected @endif>{{$item->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select name="status_id" class="form-control">
                                                        <option value="">Chọn trạng thái</option>
                                                        @if(!empty($status_groups))
                                                            @foreach($status_groups as $key => $item )
                                                                <option value="{{$key}}" @if($params['status_id'] == $key) selected @endif>{{$item}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select name="sale_id" class="form-control">
                                                        <option value="">Tất cả sale</option>
                                                        @if(!empty($sales))
                                                            @foreach($sales as $key => $item )
                                                                <option value="{{$key}}" @if(isset($params['sale_id']) && $params['sale_id'] == $key) selected @endif>{{$item}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <select name="has_revenue" class="form-control">
                                                        @if(!empty(REVENUE_TYPE))
                                                            @foreach(REVENUE_TYPE as $key => $item )
                                                                <option value="{{$key}}" @if(isset($params['has_revenue']) && $params['has_revenue'] == $key) selected @endif>{{$item}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right col-md-3">
                                        <button type="submit" class="btn btn-primary mr-3">Xem báo cáo</button>
                                        <button type="button" class="btn btn-default" onclick="printDiv('ifrmPrint','reportForm')"><i class="fas fa-print"></i> In</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="reportForm">
                            <div class="row">
                                @if(isset($data) && count($data) > 0)
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
                                                <h2>BẢNG KÊ SẢN PHẨM/HÀNG HÓA</h2>
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
                                    <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse" bordercolor="#999">
                                        <thead>
                                        <tr>
                                            <th width="4%" scope="col">STT</th>
                                            <th width="10%" align="center" scope="col">Mã sản phẩm, hoàng hóa </th>
                                            <th width="30%" align="center" scope="col">Tên sản phẩm, hoàng hóa </th>
                                            <th width="10%" scope="col" style="text-align: center">Đơn vị</th>
                                            <th width="10%" scope="col" style="text-align: center">Số lượng</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $quantity = 0;
                                        ?>

                                            @foreach($data as $key => $item)
                                                <tr>
                                                    <td align="center">{{$key+1}}</td>
                                                    <td align="center" nowrap="nowrap">{{$item->code}}</td>
                                                    <td align="left">{{$item->name}}</td>
                                                    <td align="center">{{$item->unit_name}}</td>
                                                    <td align="center">{{number_format($item->quantity)}}</td>
                                                </tr>
                                                <?php
                                                    $quantity += $item->quantity;
                                                ?>
                                            @endforeach
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td align="center">Tổng cộng </td>
                                            <td align="center">x</td>
                                            <td align="center">x</td>
                                            <td align="center"><strong>{{number_format($quantity)}}</strong></td>
                                        </tr>
                                        </tbody></table>
                                </div>
                                @else
                                <div class="col-md-12">
                                    <div class="alert text-center text-danger">Không có kết quả quản phù hợp!</div>
                                </div>
                                @endif
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
