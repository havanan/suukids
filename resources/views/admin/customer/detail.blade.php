@extends('layout.default')
@section('title') Admin | Thông tin khách hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Thông tin khách hàng'),
            'content' => [
                __('Thông tin khách hàng') => route('admin.customer.detail',$info->id)
            ],
            'active' => [__('Thông tin khách hàng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <script src="{{url('js/source.js')}}"></script>
    <link href="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ url('theme/admin-lte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{url('js/customer/detail.js')}}"></script>
    <script src="{{url('js/customer/index.js')}}"></script>
    <script>
        $("#image_url").change(function () {
            readImageUrl(this);
        });
        Common.datePicker('.date-picker');
        const urlGetDetailNote = '{{ route('admin.customer.detail.note') }}'
        const urlGetDetailCall = '{{ route('admin.customer.detail.call') }}'
        const urlStoreCall = '{{route('admin.customer.save.call')}}';
        const urlStoreNote = '{{route('admin.customer.save.note')}}';
        const urlListNote = '{{ route('admin.customer.detail',['tab'=>'note','id'=>$info->id])}}'
        const urlListCall = '{{ route('admin.customer.detail',['tab'=>'call','id'=>$info->id])}}'
        const urlListPathological = '{{ route('admin.customer.detail',['tab'=>'pathological','id'=>$info->id])}}'
        const urlGetHistoryNote = '{{route('admin.customer.history.note')}}';
        const urlGetHistoryCall = '{{route('admin.customer.history.call')}}';
        const CUSTOMER_EMOTIONS = JSON.parse('{!! json_encode(CUSTOMER_EMOTIONS) !!}')
        const customerCareStatus = JSON.parse('{!! json_encode($customerCareStatus) !!}')
        const urlStoreformPathological = '{{route('admin.customer.save.pathological')}}';
        const urlGetDetailPathological = '{{route('admin.customer.detail.pathological')}}';
    </script>

    <style>
        .emoji{
            font-size: 35px;
        }
    </style>

    @include('layout.flash_message')

@stop

@section('content')
    <section class="content">
        <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-right">
                                <a href="{{route('admin.customer.index')}}"><button type="button" class="btn btn-default">Quay lại</button></a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <img src="@if(isset($info->avatar)){{url($info->avatar)}}
                                        @else {{url('theme/admin-lte/dist/img/no_avatar.webp')}}
                                        @endif"
                                             id="imagePreview"
                                             class="customer-avatar">

                                        <div class="card mt-3">
                                          <table class="table table-striped">

                                            <tbody>
                                              <tr>
                                                <td class="text-right" width="50%">Tên:	</td>
                                                <td class="text-left"width="50%">{{$info->name}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Đơn hàng mới nhất:</td>
                                                <td class="text-left" width="50%">
                                                    @if(isset($info->orders->last()->code))
                                                    <span>MÃ ĐH: {{ $info->orders->last()->code }}</span>
                                                    <br>
                                                    @if( $info->orders->last()!== null && $info->orders->last()->orderStatusName !== null)
                                                    <small style="color: {{ $info->orders->last()->orderStatusName->color }}"><i class="far fa-clock"></i>
                                                        {{ $info->orders->last()->orderStatusName->name }}
                                                    </small>
                                                    @endif
                                                @endif
                                                </td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Nhóm phân loại:</td>
                                                <td class="text-left" width="50%">
                                                    @if($info->customer_group_id && isset($info->customerGroup))
                                                        {{$info->customerGroup->name}}
                                                    @endif
                                                </td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Di động:	</td>
                                                <td class="text-left" width="50%">{{$info->phone}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Nghề nghiệp:</td>
                                                <td class="text-left" width="50%">{{$info->job}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Chức vụ:</td>
                                                <td class="text-left" width="50%">{{$info->position}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Cân nặng:	</td>
                                                <td class="text-left" width="50%">{{$info->weight}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Ngày sinh:</td>
                                                <td class="text-left" width="50%">{{$info->birthday}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Email:</td>
                                                <td class="text-left" width="50%">{{$info->email}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Khu vực:</td>
                                                <td class="text-left" width="50%">
                                                    @if(isset($info->province))
                                                        {{$info->province->_name}}
                                                    @endif
                                                </td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Địa chỉ:</td>
                                                <td class="text-left" width="50%">{{$info->address}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Thuộc:	</td>
                                                <td class="text-left" width="50%"></td>
                                              </tr>
                                              <tr>
                                                <td class="text-right text-danger">Ghi chú cảnh báo:</td>
                                                <td class="text-left" width="50%">{{$info->note_alert}}</td>
                                              </tr>
                                              <tr>
                                                <td class="text-right" width="50%">Ghi chú chung:</td>
                                                <td class="text-left" width="50%">{{$info->note}}</td>
                                              </tr>
                                            </tbody>
                                          </table>
                                        </div>

                                        <div class="card">
                                          <div class="card-header card-header-default">
                                              <h3 class="card-title">
                                                  <strong>Ngân hàng</strong>
                                              </h3>
                                          </div>
                                          <div class="card-body" style="max-height: 254px;overflow-y: auto">
                                            <table class="table table-striped">
                                              <tbody>
                                                <tr>
                                                  <td class="" width="50%">Tên ngân hàng:	</td>
                                                  <td class="text-left"width="50%">{{$info->bank_name}}</td>
                                                </tr>
                                                <tr>
                                                  <td class="" width="50%">Số tài khoản ngân hàng:</td>
                                                  <td class="text-left" width="50%">{{$info->bank_account_number}}</td>
                                                </tr>
                                                <tr>
                                                  <td class="" width="50%">Tên tài khoản ngân hàng:</td>
                                                  <td class="text-left" width="50%">{{$info->bank_account_name}}</td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                              <a class="nav-link {{ !in_array(request()->get('tab'),['note','call','pathological']) ? 'active' : '' }}" href="#order" role="tab" data-toggle="tab"><i class="fas fa-tags"></i> Đơn hàng</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link {{request()->get('tab') == 'note' ? 'active' : '' }}" href="#note" role="tab" data-toggle="tab"><i class="far fa-file-alt"></i> Ghi chú</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link {{request()->get('tab') == 'call' ? 'active' : '' }}" href="#call" role="tab" data-toggle="tab"><i class="fas fa-phone-square-alt"></i> Cuộc gọi</a>
                                            </li>
                                          </ul>

                                          <!-- Tab panes -->
                                          <div class="tab-content">
                                              {{-- tab order --}}
                                            <div role="tabpanel" class="tab-pane {{ !in_array(request()->get('tab'),['note','call','pathological']) ? 'active' : 'fade' }}" id="order">
                                                <div class="card">
                                                    <div class="card-header card-header-default">
                                                        <h3 class="card-title">
                                                            <strong>Đơn hàng</strong>
                                                        </h3>
                                                    </div>
                                                    <div class="card-body" style="overflow-y: auto">
                                                      <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>STT</th>
                                                                <th>MÃ ĐH</th>
                                                                <th>Trạng thái</th>
                                                                <th>Thời gian</th>
                                                                <th>Ghi chú đơn</th>
                                                                <th>Tổng tiền</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $totalAmount = 0; @endphp
                                                            @foreach ($listOrder as $index => $order)
                                                                @php $totalAmount += $order->total_price; @endphp
                                                                <tr>
                                                                    <td>{{$index+1}}</td>
                                                                    <td>{{$order->code}}</td>
                                                                    <td>{{isset($order->status->name) ? $order->status->name : ''}}</td>
                                                                    <td>{{$order->create_date}}</td>
                                                                    <td>{{$order->note1}}</td>
                                                                    <td>{{number_format($order->total_price)}}</td>
                                                                </tr>
                                                            @endforeach
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td>Tổng tiền: </td>
                                                                    <td>{{number_format($totalAmount)}}</td>
                                                                </tr>
                                                        </tbody>
                                                      </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Tab note -->
                                            <div role="tabpanel" class="tab-pane {{request()->get('tab') == 'note' ? 'active' : 'fade' }}" id="note">
                                                <div class="card">
                                                    <div class="card-header card-header-default">
                                                        <h3 class="card-title">
                                                            <strong>Ghi chú</strong>
                                                        </h3>
                                                    </div>
                                                    <div class="card-body" style="overflow-y: auto">
                                                        <div class="timeline">
                                                            @foreach ($noteHistory as $item)
                                                                <div>
                                                                    <i class="fas fa-file-alt bg-blue"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time mr-3" @if(!isset($item->createBy->name))style="padding:0px" @endif><i class="fas fa-clock"></i> {{$item->date_create}}</span>
                                                                        <h3 class="timeline-header">{{ isset($item->createBy->name)?$item->createBy->name :'' }}</h3>

                                                                        <div class="timeline-body">
                                                                            <div class="row">
                                                                                <div class="col-md-9">
                                                                                    {!!$item->content!!}
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    @if($item->customer_emotions == 1)
                                                                                        <i class="far fa-meh emoji  "></i>
                                                                                    @elseif($item->customer_emotions == 2)
                                                                                        <i class="far fa-grin emoji text-success" style="font-size: 35px;"></i>
                                                                                    @elseif($item->customer_emotions == 3)
                                                                                        <i class="far fa-frown-open emoji text-danger"></i>
                                                                                    @endif
                                                                                    <button type="button" data-id="{{$item->id}}" class="btn btn-default btn-sm edit-note"><i class="far fa-edit"></i> Cập nhật</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="text-right">
                                                            <button type="button" data-id="{{$info->id}}" data-name="{{$info->name}}" id="openNote" class="btn btn-primary"><i class="fa fa-plus mr-2"></i> Thêm mới</button>
                                                            <a href="{{ route('admin.customer.detail.list.note',$info->id)}}" class="btn btn-warning"><i class="fas fa-list"></i> Tất cả</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Tab call -->
                                            <div role="tabpanel" class="tab-pane {{request()->get('tab') == 'call' ? 'active' : 'fade' }}" id="call">
                                                <div class="card">
                                                    <div class="card-header card-header-default">
                                                        <h3 class="card-title">
                                                            <strong>Cuộc gọi</strong>
                                                        </h3>
                                                    </div>
                                                    <div class="card-body" style="overflow-y: auto">
                                                        <div class="timeline">
                                                            @foreach ($callHistory as $item)
                                                                <div>
                                                                    <i class="fas fa-phone-square-alt bg-blue"></i>
                                                                    <div class="timeline-item">
                                                                        <span class="time mr-3" @if(!isset($item->createBy->name))style="padding:0px" @endif><i class="fas fa-clock"></i> {{$item->date_create}}</span>
                                                                        <h3 class="timeline-header">{{ isset($item->createBy->name)?$item->createBy->name :'' }}</h3>

                                                                        <div class="timeline-body">
                                                                            <div class="row">
                                                                                <div class="col-md-9">
                                                                                    {!!$item->content!!}
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    @if($item->customer_emotions == 1)
                                                                                        <i class="far fa-meh emoji  "></i>
                                                                                    @elseif($item->customer_emotions == 2)
                                                                                        <i class="far fa-grin emoji text-success" style="font-size: 35px;"></i>
                                                                                    @elseif($item->customer_emotions == 3)
                                                                                        <i class="far fa-frown-open emoji text-danger"></i>
                                                                                    @endif
                                                                                    <button data-toggle="modal" data-target="#modelCall" type="button" data-id="{{$item->id}}" class="btn btn-default btn-sm edit-call"><i class="far fa-edit"></i> Cập nhật</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="text-right">
                                                            <button type="button" data-id="{{$info->id}}" data-name="{{$info->name}}" id="openCall" class="btn btn-primary"><i class="fa fa-plus mr-2"></i> Thêm mới</button>
                                                            <a href="{{ route('admin.customer.detail.list.call',$info->id)}}" class="btn btn-warning"><i class="fas fa-list"></i> Tất cả</a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
        </div><!-- /.container-fluid -->
    </section>

    <!-- Modal Call-->
    @includeIf('admin.customer.modal_call')
    <!-- End Modal Call-->
    <!-- Modal Note-->
    @includeIf('admin.customer.modal_note')
    <!-- End Modal Note-->
    <!-- Modal pathological-->
    @includeIf('admin.customer.modal_pathological')
    <!-- End Modal pathological-->
@stop
