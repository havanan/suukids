@extends('layout.default')
@section('title') Admin | Quản lý shop @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý shop'),
            'content' => [
                __('Quản lý shop') => route('admin.shop.index')
            ],
            'active' => [__('Quản lý shop')]
        ];
    @endphp

    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')

  <script>
    const urlStoreShop = '{{route('admin.shop.index')}}';
  </script>
  <script src="{{ url("js/order_status.js") }}"></script>
  @include('layout.flash_message')
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- /.modal -->
                            <div class="text-center" style="float: right">
                              <button type="button"id="btn-save-shop"  
                              class="btn btn-primary btn-custom-color float-right"><i class="far fa-save"></i> Lưu</button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form method="post" action="{{route('admin.shop.index')}}" id="frm-data-shop">
                                @csrf
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="w-40">STT</th>
                                        <th >Tên</th>
                                        <th >Địa chỉ</th>
                                        <th>Số điện thoại</th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                    @if(isset($shop))
                                        
                                      <tr>
                                          <td class="text-center">1</td>
                                          <td>
                                              <input type="text" value="{{$shop->name}}" class="form-control" name="name" >
                                              <input type="hidden" name="id" value="{{$shop->id}}">
                                          </td>
                                          <td>
                                              <input type="text" value="{{$shop->address}}" class="form-control" name="address" >
                                          </td>
                                          <td>
                                            <input type="text" value="{{$shop->phone}}" class="form-control" name="phone" >
                                          </td>
                                      </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </form>
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
