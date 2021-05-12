@extends('layout.default')
@section('title') Admin | Quản lý cuộc gọi @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
'title' => __('Quản lý cuộc gọi'),
'content' => [
__('Quản lý cuộc gọi') => route('admin.customer.detail.list.call',$customerId)
],
'active' => [__('Quản lý cuộc gọi')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
  <link rel="stylesheet" href="{{ url('css/source.css') }}">
  <link href="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{url('js/customer/detail.js')}}"></script>
    <script src="{{url('js/customer/index.js')}}"></script>

<script>
    const urlGetDetailNote = '{{ route('admin.customer.detail.note') }}'
    const urlGetDetailCall = '{{ route('admin.customer.detail.call') }}'
    const urlStoreCall = '{{route('admin.customer.save.call')}}';
    const urlStoreNote = '{{route('admin.customer.save.note')}}';
    // const urlListNote = '{{ route('admin.customer.detail',['tab'=>'note','id'=>$customerId])}}'
    const urlListCall = '{{ route('admin.customer.detail.list.call',$customerId)}}'
    const urlGetHistoryNote = '{{route('admin.customer.history.note')}}';
    const urlGetHistoryCall = '{{route('admin.customer.history.call')}}';
    const CUSTOMER_EMOTIONS = JSON.parse('{!! json_encode(CUSTOMER_EMOTIONS) !!}')
    const customerCareStatus = JSON.parse('{!! json_encode($customerCareStatus) !!}')
</script>
<style>
  .emoji{
      font-size: 35px;
  }
  .list-group-item{
    padding: .75rem 0.5rem!important;
  }
</style>
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
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="text-right">
                                  <button type="button" data-id="{{$customerId}}" data-name="{{$customer->name}}" id="openCall" class="btn btn-primary"><i class="fa fa-plus mr-2"></i> Thêm mới</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <form action="{{ route('admin.customer.detail.list.call',$customerId) }}" action="get" style="display:contents">
                              <div class="col-md-3">
                                <input type="text" id="name" class="form-control" name="keyword" value="{{ request()->get('keyword') }}" placeholder="Nhập tên KH, SĐT,ID...">
                              </div>
                              <div class="col-md-3" id="status" >
                                  <select class="form-control" name="status">
                                      <option value="">Tất cả</option>
                                      @foreach ($customerCareStatus as $id => $name)
                                          <option {{ request()->get('status') == $id ? 'selected' : '' }} value="{{$id}}">{{$name}}</option>
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
                              <div class="col-md-2 text-left">
                                  <button class="btn btn-default" type="submit"><i class="fa fa-search mr-2"></i>Tìm
                                      kiếm</button>
                              </div>
                          </form>
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-11">
                          <table id="example1" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>ID</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Tên KH</th>
                                    <th>SĐT KH</th>
                                    <th>Cảm Xúc KH</th>
                                    <th>Nội Dung</th>
                                    <th>Người tạo</th>
                                    <th>Sửa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($callData as $index => $itemCall)
                                    <tr>
                                        <td>{{$index + 1}}</td>
                                        <td class="text-center">
                                          {{$itemCall->id}}
                                        </td>
                                        <td>
                                          {{$itemCall->created_at}}
                                        </td>
                                        <td>{{$itemCall->customer_care_id ? CUSTOMER_CARE[$itemCall->customer_care_id] :''}}</td>
                                        <td>
                                          {{$itemCall->customers_name}}
                                        </td>
                                        <td>
                                          {{$itemCall->phone}}
                                        </td>
                                        <td class="text-center">
                                          @if($itemCall->customer_emotions == 1)
                                            <i class="far fa-meh emoji  "></i>
                                          @elseif($itemCall->customer_emotions == 2)
                                              <i class="far fa-grin emoji text-success" ></i>
                                          @elseif($itemCall->customer_emotions == 3)
                                              <i class="far fa-frown-open emoji text-danger"></i>
                                          @endif   
                                        </td>
                                        <td>
                                          {!!$itemCall->content!!}
                                        </td>
                                        <td>
                                          {{$itemCall->create_by_name}}
                                        </td>
                                        <td>
                                          <button data-toggle="modal" data-target="#modelCall" type="button" data-id="{{$itemCall->id}}" class="btn btn-default btn-sm edit-call ml-3"><i class="far fa-edit"></i> Cập nhật</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-1">
                          <ul class="list-group">
                            <li class="list-group-item text-center">
                              <a class="btn-warning btn btn-lg" href="{{ route('admin.customer.detail.list.note',$customerId)}}" title="Ghi chú">
                                <i class="far fa-file-alt"></i></a>
                            </li>
                            <li class="list-group-item text-center">
                              <a class="btn-success btn btn-lg" href="{{ route('admin.customer.detail.list.call',$customerId)}}" title="Lịch sử cuộc gọi">
                              <i class="fa fa-phone-square"></i></a>
                            </li>
                            <li class="list-group-item text-center">
                              <a class="btn-info btn btn-lg" href="{{route('admin.customer.detail.list.pathological',$customerId)}}" title="Bệnh lý">
                              <i class="fa fa-flask"></i></a>
                            </li>
                          </ul>
                        </div>
                      </div>
                      
                      {{ $callData->links() }}
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
