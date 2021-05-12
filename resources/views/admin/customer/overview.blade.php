@extends('layout.default')
@php
use Illuminate\Support\Arr
@endphp
@section('title') Admin | Chăm sóc khách hàng @stop

@section('assets')
<script src="{{url('js/customer/overview.js')}}"></script>
@include('layout.flash_message')
<script>
    const urlOverviewUpdate = '{{ route('admin.customer.overviewUpdate') }}'
    const urlOverviewTagAdd = '{{ route('admin.customer.overviewTagAdd') }}'
    const urlOverviewTagUpdate = '{{ route('admin.customer.overviewTagUpdate') }}'
</script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <form id="frmSearch" action="{{ route('admin.customer.overview') }}" action="get" style="display:contents">
                                <div class="col-md-12">
                                    <div class="" style="display:flex;">
                                        <div class="col">
                                            <input type="text" id="name" class="form-control" name="name" value="{{ request()->get('name') }}" placeholder="Họ tên">
                                        </div>
                                        <div class="col">
                                            <input type="text" id="phone" class="form-control" value="{{ request()->get('phone') }}" name="phone" placeholder="Số điện thoại">
                                        </div>
                                        <div class="col">
                                            <select class="form-control select2" name="assigned">
                                                <option value="">- Tất cả sale -</option>
                                                <option value="-1" {{ request()->input('assigned') == -1 ? 'selected' : '' }}>- Bỏ chia/ Không chia -</option>
                                                @foreach($user_list as $user)
                                                @if ($user->isSale())
                                                <option {{ request()->input('assigned') == $user->id ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-control select2" name="returned">
                                                <option value="">- Lần mua -</option>
                                                @for($i=0;$i<10;$i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-control select2" name="marketing">
                                                <option value="">- Tất cả mkt -</option>
                                                @foreach($user_list as $user)
                                                @if ($user->isMarketing())
                                                <option {{ request()->input('assigned') == $user->id ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-control select2" name="staff">
                                                <option value="">- Tất cả NV -</option>
                                                @foreach($user_list as $user)
                                                <option {{ request()->input('assigned') == $user->id ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-control select2" name="source">
                                                <option value="">- Tất cả nguồn -</option>
                                                @foreach($source_arr as $source)
                                                <option {{ request()->input('source') == $source->id ? 'selected' : '' }} value="{{ $source->id }}">{{ $source->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin: 15px 0; border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;overflow: auto;">
                                        @foreach($status_arr as $status)
                                        <div class="col-2 p-1" style="font-size: 13px;border-left: 5px solid {{ $status->color }}; color:{{ $status->color }};">
                                            <label class="mb-0 align-left">
                                                <input type="checkbox" {{ in_array($status->id,request()->input('status_arr')?:[]) ? 'checked' : '' }} name="status_arr[]" value="{{ $status->id }}" class="">
                                                {{ $status->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row" style="margin: 15px 0; border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;overflow: auto;">
                                        @foreach($product_arr as $product)
                                        <div class="col-2 p-1">
                                            <label class="mb-0 align-left">
                                                <input type="checkbox" {{ in_array($product->id,request()->input('product_arr')?:[]) ? 'checked' : '' }} name="product_arr[]" value="{{ $product->id }}" class="">
                                                {{ $product->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row" style="margin: 15px 0; border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;overflow: auto;">
                                        @foreach($bundle_arr as $bundle)
                                        <div class="col-2 p-1">
                                            <label class="mb-0 align-left">
                                                <input type="checkbox" {{ in_array($bundle->id,request()->input('bundle_arr')?:[]) ? 'checked' : '' }} name="bundle_arr[]" value="{{ $bundle->id }}" class="">
                                                {{ $bundle->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row" style="margin: 15px 0; border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;overflow: auto;">
                                        @for($i=0;$i < 12;$i++)
                                        <div class="col-1 p-1">
                                            <label class="mb-0 align-left">
                                                <input type="checkbox" {{ in_array($i,request()->input('month_arr')?:[]) ? 'checked' : '' }} name="month_arr[]" value="{{ $i }}" class="">
                                                {{ date('m/Y',strtotime('-'.(11-$i).' months')) }}
                                            </label>
                                        </div>
                                        @endfor
                                    </div>
                                    <div class="row">
                                        <div class="col date-filter-box">
                                            <div class="card card-secondary m-0">
                                                <div class="card-header p-1">
                                                    <label for="create_date_checkbox">
                                                        <input name="create_date_checkbox" id="create_date_checkbox" type="checkbox" value="" onclick="reloadList()"> Ngày tạo
                                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày data số/đơn hàng được tạo">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="card-body p-2">
                                                    <input name="c_from" value="{{ request()->input('c_from') }}" class="form-control datepicker">
                                                    <input name="c_to" value="{{ request()->input('c_to') }}" class="form-control datepicker">
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="col date-filter-box">
                                            <div class="card card-secondary m-0">
                                                <div class="card-header p-1">
                                                    <label for="share_date_checkbox">
                                                        <input name="share_date_checkbox" id="share_date_checkbox" type="checkbox" value="" onclick="reloadList()"> Ngày chia
                                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày data số/đơn hàng được chia cho sale xử lý">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="card-body p-2">
                                                    <input name="s_from" value="{{ request()->input('s_to') }}" class="form-control datepicker">
                                                    <input name="s_to" value="{{ request()->input('s_to') }}" class="form-control datepicker">
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="col date-filter-box">
                                            <div class="card card-secondary m-0">
                                                <div class="card-header p-1">
                                                    <label for="close_date_checkbox">
                                                        <input name="close_date_checkbox" id="close_date_checkbox" type="checkbox" value="" onclick="reloadList()"> Ngày chốt
                                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được hàng được chốt">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="card-body p-2">
                                                    <input name="d_from" value="{{ request()->input('d_to') }}" class="form-control datepicker">
                                                    <input name="d_to" value="{{ request()->input('d_to') }}" class="form-control datepicker">
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="col date-filter-box">
                                            <div class="card card-secondary m-0">
                                                <div class="card-header p-1">
                                                    <label for="delivery_date_checkbox">
                                                        <input name="delivery_date_checkbox" id="delivery_date_checkbox" type="checkbox" value="" onclick="reloadList()"> Ngày chuyển hàng
                                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Chuyển hàng (không tính với trạng thái do shop tự tạo)">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="card-body p-2">
                                                    <input name="t_from" value="{{ request()->input('t_to') }}" class="form-control datepicker">
                                                    <input name="t_to" value="{{ request()->input('t_to') }}" class="form-control datepicker">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col date-filter-box">
                                            <div class="card card-secondary m-0">
                                                <div class="card-header p-1">
                                                    <label for="collect_money_date_checkbox">
                                                        <input name="collect_money_date_checkbox" id="collect_money_date_checkbox" type="checkbox" value="" onclick="reloadList()"> Ngày thu tiền
                                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đã thu tiền (không tính với trạng thái do shop tự tạo)">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="card-body p-2">
                                                    <input name="m_from" value="{{ request()->input('m_to') }}" class="form-control datepicker">
                                                    <input name="m_to" value="{{ request()->input('m_to') }}" class="form-control datepicker">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <datalist id="list_tags">
                    @foreach($bundle_arr as $bundle)
                    <option value="{{ $bundle->name }}">
                    @endforeach
                    </datalist>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" style="width: max-content !important;">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Điện thoại</th>
                                        <th>Lần mua</th>
                                        <th>Địa chỉ</th>
                                        <th>App</th>
                                        <th>Bệnh</th>
                                        <th>Nguồn</th>
                                        <th>Sản phẩm</th>
                                        <th>Trạng thái</th>
                                        <th>NV được chia</th>
                                        <th>Nguồn upsale</th>
                                        <th class="hidden">LS thuốc ngoài</th>
                                        <th class="hidden">LS mua thuốc</th>
                                        <th>Trạng thái sử dụng</th>
                                        <th>Nghề nghiệp</th>
                                        <th>Tài chính</th>
                                        <th>Lời nhắc/Lời khuyên</th>
                                        <th>Người giới thiệu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr data-customer_id="{{ $item->customer_id }}" class="{{ $item->duplicated ? 'bg-grey' : 'bg-white' }}">
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>{{ $item->customer ? $item->customer->name : '' }}</td>
                                        <td>
                                            <div style="border: 1px solid {{ $item->status->color }}; border-left: 3px solid {{ $item->status->color }}; color: {{ $item->close_duplicated_order_id ? 'red' : 'black' }};"><span>{{ $item->customer ? $item->customer->phone : '' }} {{ $item->customer ? $item->customer->phone2 : '' }}</span></div>
                                        </td>
                                        <td>{{ $item->returned ?: '0' }}</td>
                                        <td>{{ $item->customer ? $item->customer->address : '' }}</td>
                                        <td>
                                            <select onchange="updateOverview(this)" data-key="{{ $item->customer_id }}" data-column="app_installed" class="form-control">
                                                <option value="0" {{ $item->customer && $item->customer->app_installed == 1 ? '' : 'selected' }}>Chưa cài</option>
                                                <option value="1" {{ $item->customer && $item->customer->app_installed == 0 ? '' : 'selected' }}>Đã cài</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="">
                                                @foreach($bundle_arr as $tag)
                                                <span width="50px">
                                                    <input {{ $item->customer && $item->customer->tags && in_array($tag->id, $item->customer->tags_arr()) ? 'checked' : '' }} onchange="addTag(this)" data-value="{{ $tag->id }}" data-key="{{ $item->customer_id }}" type="checkbox" >
                                                    {{ $tag->name }}
                                                </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            {{ $item->source ? $item->source->name : '' }}
                                        </td>
                                        <td>
                                            {{ $item->products_name }}
                                        </td>
                                        <td>
                                            <span style="border: 1px solid {{ $item->status->color }};border-left: 3px solid {{ $item->status->color }};">{{ $item->status->name }}</span>
                                        </td>
                                        <td>{{ $item->assigned_user ? $item->assigned_user->name : '' }}</td>
                                        <td>{{ $item->upsale_from_user ? $item->upsale_from_user->name : '' }}</td>
                                        <td>
                                            <select onchange="updateOverview(this)" data-key="{{ $item->customer_id }}" data-column="medical_condition" class="form-control">
                                                <option value="" selected>Chọn</option>
                                                @foreach($conditions as $condition)
                                                <option value="{{ $condition->id }}" {{ $item->customer && $item->customer->medical_condition == $condition->id ? 'selected' : '' }}>{{ $condition->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            {!! $item->customer ? $item->customer->job : '' !!}
                                        </td>
                                        <td>
                                            <select onchange="updateOverview(this)" data-key="{{ $item->customer_id }}" data-column="buy_capacity" class="form-control">
                                                <option value="" selected>Chọn</option>
                                                @foreach($level_arr as $level)
                                                <option value="{{ $level->id }}" {{ $item->customer && $item->customer->buy_capacity == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            {!! $item->customer ? $item->customer->sale_note : '' !!}
                                        </td>
                                        <td>
                                            {!! $item->customer ? $item->customer->referral : '' !!}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $items->appends($_GET)->links() }}
                        </div>
                    </div>
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
<style>
    body{
        font-size: 14px;
    }
    td,th{
        font-size: 14px;
    }
    .form-control {
        height: 31px !important;
        font-size: 14px !important;
        line-height: 25px !important;
        padding: .1rem .3rem !important;
    }
    .bg-grey{
        background-color:#dee2e6;
    }
    .card-body .datepicker{
        width: 48%;
        display: inline-block;
        height: 30px;
        border-radius: 0;
    }
    .date-filter-box {
        width: 14.1%;
        padding: 5px;
    }
    .date-filter-box label, .date-filter-box input {
        font-size: 13px;
    }
</style>
