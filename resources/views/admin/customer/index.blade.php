@extends('layout.default')
@section('title') Admin | Danh sách khách hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Danh sách khách hàng'),
'content' => [
__('Danh sách khách hàng') => route('admin.customer.index')
],
'active' => [__('Danh sách khách hàng')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
<link rel="stylesheet" href="{{ url('css/source.css') }}">
<link href="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet" type="text/css">
<script src="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{url('js/customer/index.js')}}"></script>
@include('layout.flash_message')
<script>
    const urlStoreCall = '{{route('admin.customer.save.call')}}';
    const urlStoreNote = '{{route('admin.customer.save.note')}}';
    const urlList = '{{route('admin.customer.index')}}';
    const urlGetHistoryCall = '{{route('admin.customer.history.call')}}';
    const urlGetHistoryNote = '{{route('admin.customer.history.note')}}';
    const CUSTOMER_EMOTIONS = JSON.parse('{!! json_encode(CUSTOMER_EMOTIONS) !!}');
    const urlDelete = '{{ route('admin.customer.delete') }}'
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
                                    {{-- <button class="btn btn-warning" style="min-width: 100px;" id="btn-delete">Xóa</button> --}}
                                    {{-- <button class="btn btn-danger" id="btn-delete-all">Xóa Tất Cả</button> --}}
                                    <a class="btn btn-default"
                                        href="{{ route('admin.customer.index',['birth'=>'today']) }}"><i
                                            class="fa fa-calendar-alt mr-2"></i>SN hôm
                                        nay</a>
                                    <a class="btn btn-default"
                                        href="{{ route('admin.customer.index',['birth'=>'week']) }}"><i
                                            class="fa fa-calendar-alt mr-2"></i>SN tuần
                                        này</a>
                                    <a href="{{route('admin.customer.create')}}" class="btn btn-primary"><i
                                            class="fa fa-plus mr-2"></i> Thêm mới</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <form action="{{ route('admin.customer.index') }}" action="get" style="display:contents">
                                    @if(!empty($query['method']) && $query['method'] == 'select')
                                @foreach($query as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                @endif
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <select class="form-control" name="type" style="border: 1px dashed red;">
                                                <option value="">Phân loại</option>
                                                @foreach ( CUSTOMER_TYPE as $index => $name)
                                                    <option value="{{$index}}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="created_from" class="form-control datepicker" name="created_from"
                                                placeholder="Từ ngày" value="{{ request()->get('created_from') }}"
                                            >
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="created_to" class="form-control datepicker" name="created_to"
                                            placeholder="Đến ngày" value="{{ request()->get('created_to') }}"
                                                >
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="name" class="form-control" name="name" value="{{ request()->get('name') }}" placeholder="Họ tên">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="phone" class="form-control" value="{{ request()->get('phone') }}" name="phone" placeholder="Số điện thoại">
                                        </div>
                                        <div class="col-md-2" id="customer_group_id" >
                                            <select class="form-control" name="customer_group_id">
                                                <option value="">Xem theo nhóm</option>
                                                @foreach ($customerGroup as $id => $name)
                                                    <option {{ request()->get('customer_group_id') == $id ? 'selected' : '' }} value="{{$id}}">{{$name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <button class="btn btn-default" type="submit"><i class="fa fa-search mr-2"></i>Tìm
                                        kiếm</button>
                                    <a href="{{ route('admin.customer.index') }}" class="btn btn-default"><i
                                            class="fa fa-refresh mr-2"></i>Tìm lại</a>
                                    {{-- <button class="btn btn-default"><i class="fa fa-refresh mr-2"></i>Tìm lại</button> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover mb-3">
                            <thead>
                                <tr>
                                    <td width="1%" title="Chọn tất cả">
                                        <input type="checkbox" value="1" id="CrmCustomerGroup_all_checkbox" >
                                      </td>
                                    <th>Khách hàng</th>
                                    <th>ĐH mới nhất</th>
                                    <th>Phân nhóm</th>
                                    <th>Phụ trách</th>
                                    <th>Người tạo</th>
                                    <th>Xem</th>
                                    <th>Note</th>
                                    <th>C.gọi</th>
                                    <th>Sửa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($listCustomer))
                                    <tr class="text-center">
                                        <td colspan="10">Không có dữ liệu</td>
                                    </tr>
                                @else
                                    @foreach ($listCustomer as $index => $customer)
                                        <tr>
                                            <td><input type="checkbox" name="delete[]" value="{{$customer->id}}"  id="checkbox-{{$index}}">
                                                <label for="checkbox-{{$index}}"></label></td>
                                            <td class="text-left">
                                                @if(!empty($query['method']) && $query['method'] == 'select')
                                                <button class="btn btn-warning text-white"
                                                    onclick="window.opener.selectContactId('{{$customer->id}}','{{$customer->name}}');window.close();">Chọn</button>
                                                <br>
                                                @endif
                                                <span class="text-bold" id="name_42575917">{!! $customer->name !!} </span><br>
                                                <span class="small">SHOP: </span><br>
                                                <span><i class="fa fa-phone-square"></i> {{ $customer->phone}} </span><br>
                                                <span>+ Đơn hàng thành công: </span><span class="badge badge-secondary">{{ $customer->orders->whereIn('status_id',STATUS_DON_HANG_THANH_CONG)->count()}}</span>
                                                <span class="small"></span>
                                                @if( $customer->email)
                                                    <div class="small text-gray">+ ĐC: {{ $customer->email}}</div>
                                                @endif
                                                <br>
                                            </td>
                                            <td class="text-center">
                                                @if(isset($customer->orders->last()->id))
                                                    <a href="{{route('admin.sell.order.edit',$customer->orders->last()->id)}}">{{ $customer->orders->last()->code }}</a>
                                                    <br>
                                                    @if( $customer->orders->last() && $customer->orders->last()->orderStatusName)
                                                    <small class="badge" style="color:{{ $customer->orders->last()->orderStatusName->color }}"><i class="far fa-clock"></i>
                                                        {{ $customer->orders->last()->orderStatusName->name }}
                                                    </small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $customer->customer_groups_name ? $customer->customer_groups_name : '-'}}
                                            </td>
                                            <td>
                                                {{ $customer->user_confirm_name ? $customer->user_confirm_name : '-'}}
                                            </td>
                                            <td>
                                                {{ $customer->created_by_name ? $customer->created_by_name : '-'}} <br>
                                                <small class="text-gray"> lúc {{ $customer->created_at}}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.customer.detail', $customer->id) }}"><button class="btn btn-primary"><i class="fa fa-eye"></i></button></a>

                                            </td>
                                            <td>
                                                <button id="openNote" class="btn btn-warning text-white"
                                                    data-name = "{{ $customer->name}}"
                                                    data-id = "{{ $customer->id}}"
                                                ><i class="fa fa-newspaper"></i></button><br>
                                                <span>{{ isset($customer->noteHistories->last()->date_create) ? $customer->noteHistories->last()->date_create : ''}}</span><br>
                                            </td>
                                            <td>
                                                <button id="openCall" class="btn btn-success"
                                                    data-name = "{{ $customer->name}}"
                                                    data-id = "{{ $customer->id}}"
                                                ><i class="fa fa-phone-square"></i></button><br>
                                                <span>{{ isset($customer->noteHistories->last()->date_create) ? $customer->callHistories->last()->date_create : ''}}</span><br>
                                                <span>{{ $customer->callHistories->last() && $customer->callHistories->last()->customer_care_id ? CUSTOMER_CARE[$customer->callHistories->last()->customer_care_id] : ''}}</span>
                                            </td>
                                            <td>
                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                        {{ $listCustomer->appends($_GET)->links() }}
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
    <!-- Modal Call-->
    @includeIf('admin.customer.modal_call')
    <!-- End Modal Call-->

    <!-- Modal Note-->
    @includeIf('admin.customer.modal_note')
    <!-- End Modal Note-->
    </section>
@stop
