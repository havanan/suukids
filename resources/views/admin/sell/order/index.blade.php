@extends('layout.default')
@section('title')
{{-- Admin | Quản lý đơn hàng --}}
Cố kiếm 100 triệu
@stop
{{-- Breadcrumb --}} @section('breadcrumb')
@php $breadcrumb = [
'title' => __("Quản lý đơn hàng "),
'content' => [ __('Quản lý đơn hàng') => route('admin.sell.order.index') ],
'active' => [__('Quản lý đơn hàng')]
];

$searchDate = [
    'create' => [
        'title' => 'Ngày tạo',
        'tooltip' => 'Ngày data số/đơn hàng được tạo'
    ],
    'share' => [
        'title' => 'Ngày chia',
        'tooltip' => 'Ngày data số/đơn hàng được chia cho sale xử lý'
    ],
    'close' => [
        'title' => 'Ngày chốt',
        'tooltip' => 'Ngày đơn hàng được hàng được chốt'
    ],
    //'assign_accountant' => [
    //    'title' => 'Chuyển kế toán',
    //    'tooltip' => 'Ngày đơn hàng được chuyển về trạng thái Kế toán mặc định (không tính với trạng thái có tên là kế toán mà do shop tự tạo)'
    //],
    'delivery' => [
        'title' => 'Ngày chuyển hàng',
        'tooltip' => 'Ngày đơn hàng được chuyển về trạng thái Chuyển hàng (không tính với trạng thái do shop tự tạo)'
    ],
    'complete' => [
        'title' => 'Ngày thành công',
        'tooltip' => 'Ngày đơn hàng được chuyển về trạng thái thành công (không tính với trạng thái do shop tự tạo)'
    ],
    'collect_money' => [
        'title' => 'Ngày thu tiền',
        'tooltip' => 'Ngày đơn hàng được chuyển về trạng thái Đã thu tiền (không tính với trạng thái do shop tự tạo)'
    ],
    'refund' => [
        'title' => 'Ngày hoàn đơn',
        'tooltip' => 'Ngày hoàn đơn'
    ],
];

function getFormatter($item)
{
    switch ($item) {
        case 'stt':
            $formatter = 'indexFormat';
            break;
        case 'assigned_user':
            $formatter = 'assignedUserFormat';
            break;
        case 'bundle':
            $formatter = 'bundleFormat';
            break;
        case 'source':
            $formatter = 'sourceFormat';
            break;
        case 'customer':
            $formatter = 'customerFormat';
            break;
        case 'customer.phone':
            $formatter = 'customerPhoneFormat';
            break;
        case 'customer.returned':
            $formatter = 'customerReturnedFormat';
            break;
        case 'customer.address':
            $formatter = 'customerAddressFormat';
            break;
        case 'customer.call.history':
            $formatter = 'customerCallHistoryFormat';
            break;
        case 'order_products':
            $formatter = 'productsFormat';
            break;
        case 'status':
            $formatter = 'statusFormat';
            break;
        case 'total_price':
            $formatter = 'priceFormat';
            break;
        case 'upsale_from_user.account_id':
            $formatter = 'upsaleFormat';
            break;
        default:
            $formatter = '';
            break;
    }

    return $formatter;
}

@endphp
@stop {{-- End Breadcrumb --}}
@section('header')
<link href="{{ url('css/source.css') }}?v={{ filemtime('css/source.css') }}" rel="stylesheet" type="text/css">
<link href="{{ url('theme/admin-lte/plugins/bootstrap-table/css/bootstrap-table.min.css') }}" rel="stylesheet"
    type="text/css">
<link rel="stylesheet" href="{{ url('assets/release/css/order.css') }}?v={{ filemtime('assets/release/css/order.css') }}">
@endsection
@section('assets')
<script src="{{ url('theme/admin-lte/plugins/bootstrap-table/js/bootstrap-table.min.js') }}"></script>
<script src="{{url('js/list.js')}}?{{filemtime('js/list.js')}}"></script>
<script src="{{url('js/order.js')}}?{{filemtime('js/order.js')}}"></script>
<style>
    .date-filter-box[data-key="refund"] {
        display:none;
    }
    .date-filter-box .hasDatepicker {
        width: 48%;
        display: inline-block;
        height: 30px;
        border-radius: 0;
    }
    .date-filter-box label,.date-filter-box input{
        font-size: 13px;
    }

    @media screen and (max-width: 767px) {

    }
</style>
<script>
    let urlEdit = "{{ route('admin.sell.order.edit') }}";
    let urlUpdateStatus = "{{ route('admin.sell.order.update-status') }}";
    let urlFlashShare = "{{ route('admin.sell.order.flash-share') }}";
    let urlCountNotAss = "{{ route('admin.sell.order.count-not-assign') }}";
    let assignOrderForSaleUrl = "{{ route('admin.sell.order.assign-for-sale') }}";
    let assignOrderForMktUrl = "{{ route('admin.sell.order.assign-for-mkt') }}";
    let assignOrderForGroupUrl = "{{ route('admin.sell.order.assign-for-group') }}";
    let exportExcelUrl = "{{ route('admin.sell.order.export-excel') }}";
    let deleteOrdersUrl = "{{ route('admin.sell.order.delete-orders') }}";
    let flashEditOrderUrl = "{{ route('admin.sell.order.flash-edit') }}";
    let saveOrderSort = "{{ route('admin.sell.order.saveOrderSort') }}";
    let getTotallRevenueUrl = "{{ route('admin.sell.order.revenue') }}";
    let getOrderHistory = "{{ route('admin.sell.order.getOrderHistory') }}";
    let getOrderInfoUrl = "{{route('admin.sell.order.getInfo')}}";
    let noProcessOrderStatusId = 4;
    let urlGetListByIds = "{{ route('admin.sell.order.getListByIds') }}"
    let callCloudfoneUrl = "{{ route('admin.sell.order.call-cloudfone') }}"
    let callHistoryCloudfoneUrl = "{{ route('admin.sell.order.call-history-cloudfone') }}"
    let isAdmin = @if(getCurrentUser()->isAdmin()) 1 @else 0 @endif;
    $('#table-list').bootstrapTable({
        onLoadSuccess: function countRow() {
            countTableRow('table-list', 'count-data')
        }
    })
    $('#table-disable-list').bootstrapTable({
        onLoadSuccess: function countRow() {
            countTableRow('table-disable-list', 'disable-count-data')
        }
    })

    function showUpdateStatusModal() {
        $("#modal-orders-actions").modal('hide');
        $('#modal-status').modal('show');
    }

    // Đổi trạng thái
    $('.status-checkbox').on('change', function() {
        $('#modal-status').modal('hide');
        let selectedOrders = getSelectedOrderIds();
        if (selectedOrders.length == 0) {
            alert('Bạn chưa chọn đơn hàng nào');
            return;
        }
        let statusId = $(this).val();
        showAlertAndSendUpdateStatusAlert(selectedOrders, statusId);
        $(this).prop('checked', false);
    });

    function showAlertAndSendUpdateStatusAlert(selectedOrders, statusId) {
        Swal.fire({
            title: "Cảnh báo !",
            text: "Bạn chắc chắn muốn cập nhật trạng thái ?",
            type: "warning",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonColor: "#007bff",
            confirmButtonText: "Chắc chắn",
            cancelButtonText: "Hủy",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: urlUpdateStatus,
                    data: {
                        ids: selectedOrders,
                        status_id: statusId
                    },dataType:'json',
                    success: function(response) {
                        alertPopup('success', response.message);
                        reloadList();

                        if (response.fail_data && response.fail_data.length > 0) {
                            showErrorOrders(response.fail_data);
                        }
                    },
                    error: function(e) {
                        alertPopup('error', e.responseJSON&&e.responseJSON.message?e.responseJSON.message:'An error occurred');
                    }
                });
            }
        });
    }

    function showErrorOrders(data) {
        let ids = data.map(function (item) {
            return item.id
        });
        $.ajax({
            type: 'GET',
            url: urlGetListByIds,
            dataType: "JSON",
            data: {
                ids: ids
            },
            success: function(response) {
                var html = '';
                for (let index in response) {
                    let order = response[index];
                    let errorData = data.find(function (item) {
                        return item.id == order.id;
                    });
                    let errorMessage = errorData ? errorData.error_message : '';

                    let user_created = order.user_created_obj;
                    let customer = order.customer;
                    let status = order.status;
                    let bundle = order.bundle;
                    let bundleName = bundle ? bundle.name : '';
                    let province = order.province;
                    let district = order.district;
                    let ward = order.ward;
                    html += '<tr>'
                            +   '<td>'+index+'</td>'
                            +   '<td>'+order.code+'</td>'
                            +   '<td>'+(user_created ? user_created.account_id : '')+ '<br>' + order.created_at +'</td>'
                            +   '<td>'+(customer ? customer.name : '')+'</td>'
                            +   '<td><span class="text-info text-bold">'+customer.phone+'</span><br><span class="label" style="font-size:12px; padding:2px; background-color: #d2d6de; color: #444">'+status.name+'</span></td>'
                            +   '<td>'+bundleName+'</td>'
                            +   '<td style="max-width: 250px;">'+ (customer ? (customer.address || '') : '') +'</td>'
                            +   '<td>' + (province ? province._name : '')  +  '</td>'
                            +   '<td>' + errorMessage + '</td>'
                            +   '<td>'
                            +   '<a href="'+ order.edit_url + '?close_when_done=1' +'" target="_blank" class="btn btn-success  btn-suggest-customer"> Sửa</button>'
                            +   '</td>'
                            + '</tr>';
                }
                $('#errors-orders-table-body').html(html);
                $('#modal-errors-orders').modal('show');
            },
            error: function(e) {

            }
        });
    }

    /* Hiển thị Modal Chia đơn nhanh */
    function showFlashShareModel() {
        updateNotAssignOrders();
        $('#modal-share-orders').modal('show');
    }

    /* Chia đơn nhanh */
    function flashShare() {
        Swal.fire({
            title: "Cảnh báo !",
            text: "Bạn chắc chắn muốn chia đơn nhanh ?",
            type: "warning",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonColor: "#007bff",
            confirmButtonText: "Chắc chắn",
            cancelButtonText: "Hủy",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: urlFlashShare,
                    dataType: "JSON",
                    data: {
                        source_id: $('#flash-share-source-id').val(),
                        bundle_id: $('#flash-share-bundle-id').val(),
                        number: $('#flash-share-number-order').val(),
                        account_ids: $('#all-ass-account-id').val(),
                        group_id: $('#all-ass-group-id').val()
                    },
                    success: function(response) {
                        $('#modal-share-orders').modal('hide');
                        alertPopup('success', response.message);
                        reloadList();
                    },
                    error: function(e) {
                        alertPopup('error', e.responseJSON.message);
                    }
                });
            }
        });
    }

    /* Hiển thị actiosn Modal */
    function showActionsModal() {
        let selectedOrders = getSelectedOrderIds();
        if (selectedOrders.length == 0) {
            alert('Bạn chưa chọn đơn hàng nào');
            return;
        }

        $('.number-order-select').text(selectedOrders.length);

        $('#modal-orders-actions').modal('show');
    }

    function showSortModal() {
        $('#modal-orders-sort').modal('show');
    }

    function checkAllAndShowActionModal() {
        $('#checkAllOrder').prop('checked', true);
        checkAll($('#checkAllOrder')[0]);
        showActionsModal();
    }

    /* Chia đơn được chọn cho sale */
    function assignOrderForSale() {
        let selectedOrders = getSelectedOrderIds();
        let saleId = $('#actions-sale-id-select').val();

        if (!saleId) {
            alert('Vui lòng chọn 1 sale');
            return;
        }

        $.ajax({
            type: 'POST',
            url: assignOrderForSaleUrl,
            data: {
                ids: selectedOrders,
                sale_id: saleId
            },
            success: function(response) {
                alertPopup('success', response.message);
                $('#modal-orders-actions').modal('hide');
                reloadList();
            },
            error: function(e) {
                alertPopup('error', e.responseJSON.message);
            }
        });
    }

    /* Chia đơn được chọn cho mkt */
    function assignOrderForMarketing() {
        let selectedOrders = getSelectedOrderIds();
        let marketingId = $('#actions-mkt-id-select').val();

        if (!marketingId) {
            alert('Vui lòng chọn 1 mkt');
            return;
        }

        $.ajax({
            type: 'POST',
            url: assignOrderForMktUrl,
            data: {
                ids: selectedOrders,
                marketing_id: marketingId
            },
            success: function(response) {
                alertPopup('success', response.message);
                $('#modal-orders-actions').modal('hide');
                reloadList();
            },
            error: function(e) {
                alertPopup('error', e.responseJSON.message);
            }
        });
    }

    function assignOrderForGroup() {
        let selectedOrders = getSelectedOrderIds();
        let groupId = $('#ass_account_group').val();

        if (!groupId) {
            alert('Vui lòng chọn 1 nhóm');
            return;
        }

        $.ajax({
            type: 'POST',
            url: assignOrderForGroupUrl,
            data: {
                ids: selectedOrders,
                group_id: groupId
            },
            success: function(response) {
                alertPopup('success', response.message);
                $('#modal-orders-actions').modal('hide');
                reloadList();
            },
            error: function(e) {
                alertPopup('error', e.responseJSON.message);
            }
        });
    }

    /* Load danh sách đơn chưa được gán cho modal chia đơn nhanh */
    function updateNotAssignOrders() {
        let bundleId = $('#flash-share-bundle-id').val();
        let sourceId = $('#flash-share-source-id').val();

        $.ajax({
            type: 'GET',
            url: urlCountNotAss,
            data: {
                source_id: sourceId,
                bundle_id: bundleId
            },
            success: function(response) {
                console.log(response);
                $('#total_not_assigned_order').html(response);
                $('#flash-share-number-order').val(response);
            },
            error: function(e) {
                $('#total_not_assigned_order').html(0);
                $('#flash-share-number-order').val(response);
            }
        });
    }

    function getSelectedOrderIds() {
        var selectedOrders = [];
        $.each($("input[name='delete[]']:checked"), function() {
            selectedOrders.push($(this).val());
        });
        return selectedOrders;
    }

    function showExcelModal() {
        $('#modal-excel').modal('show');
    }

    function exportExcelOrders(isAll) {
        let type = $('#export-type-select').val();
        let selectedOrders = !isAll ? getSelectedOrderIds() : [];
        if (!isAll && selectedOrders.length <= 0) {
            alert('Bạn chưa chọn đơn hàng nào');
            return;
        }

        let query = queryParamOrderList();
        let downloadUrl = exportExcelUrl + '?' + jQuery.param({
            ids: selectedOrders,
            type: type,
            query: query
        });

        window.open(downloadUrl, '_blank');
    }

    function exportTransportExcelOrders() {
        let query = queryParamOrderList();
        let downloadUrl = exportExcelUrl + '?' + jQuery.param({
            ids: [],
            type: 'transport',
            query: query
        });

        window.open(downloadUrl, '_blank');
    }
    function setItemTop(){
        if($('#list-option-search').css('display') === 'block')
        {
            $('#table-list-order').removeClass('table-list-max').addClass('table-list-min');
        }
        else
        {
            $('#table-list-order').removeClass('table-list-min').addClass('table-list-max');
        }
    }

    /* delete order selected */
    function deleteOrders() {
        let selectedOrders = getSelectedOrderIds();

        if (selectedOrders.length <= 0) {
            alert('Bạn chưa chọn đơn hàng nào');
            return;
        }

        if(confirm("Bạn có chắc chắn muốn xóa đơn hàng đã chọn ?")){
            $.ajax({
                type: 'POST',
                url: deleteOrdersUrl,
                data: {
                    ids: selectedOrders,
                },
                success: function(response) {
                    alertPopup('success', response.message);
                    $('#modal-orders-actions').modal('hide');
                    reloadList();
                },
                error: function(e) {
                    alertPopup('error', e.responseJSON.message);
                }
            });
        }
    }

    $('a[data-toggle="tooltip"]').tooltip();

    $('.stl-row').click(function () {

        var field = $(this).attr('data-field')
        var $table = $('#table-list');
        if($(this).is(':checked')){
            $table.bootstrapTable('showColumn', field);
        } else {
            $table.bootstrapTable('hideColumn', field);
        }
    });

    $('.save-order-sort-btn').click(function () {
        let sorts = $('.order-sort-item');

        let sortArr = [];

        sorts.each(function( i,v ) {
            let show = 1;
            if (!$(this).is(":checked")) {
                show = 0;
            }

            let item = {
                name: $(this).attr('data-field'),
                show: show
            };
            sortArr.push(item);
        });

        if(confirm("Bạn có chắc chắn muốn lưu danh sách sắp xếp ?")){
            $.ajax({
                type: 'POST',
                url: saveOrderSort,
                data: {
                    sort: sortArr
                },
                success: function(response) {
                    alertPopup('success', response.message);
                    $('#modal-orders-sort').modal('hide');
                    location.reload();
                },
                error: function(e) {
                    alertPopup('error', e.responseJSON.message);
                }
            });
        }
    });

    $('.select2').select2();
    $('.select2_basic').select2({minimumResultsForSearch: Infinity});
    $( "#order_sortable" ).sortable();
    Common.formatPrice('.price');
</script>
@include('layout.flash_message') @stop @section('content')

@if(!empty($needCall))
@section('alert-header')
    <span class="alert alert-danger">Bạn có {{ $needCall }} đơn hàng cần chăm sóc lại ---> <a href="{{ route('admin.sell.order.take-care-again') }}">Vào xem</a></span>
@endsection
@endif

<section class="content order-area">
    <div class="container-fluid container-order">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-2">
                    <div class="card-header order-header">
                        <div class="row">
                            <div class="col-sm-3">
                                <input name="customer_phone_or_code" id="customer_phone_or_code"
                                       class="form-control item-search-data item-phone" placeholder="Nhập số điện thoại hoặc mã"
                                       type="text" onchange="reloadList()" value="{{ request()->get('code') }}">
                            </div>
                            <div class="col-sm-3">
                                <input name="customer_name" id="customer_name" class="form-control item-search-data"
                                       placeholder="Nhập họ tên khách hàng" type="text" onchange="reloadList()">
                            </div>
                            <div class="col-sm-3">
                                <select name="returned" onchange="reloadList()" class="form-control" id="returned">
                                    <option value="">- Lần mua -</option>
                                    @for($i=0;$i < 10;$i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-3">
                                @if(getCurrentUser()->hasPermission('share_orders'))
                                    <button type="button" class="btn btn-sm btn-default" onclick="showFlashShareModel()">
                                        <i class="fas fa-list-alt"></i>
                                        Chia đơn nhanh
                                    </button>
                                @endif
                                @if(getCurrentUser()->isAdmin())
                                    <button type="button" class="btn btn-sm btn-default" onclick="showSortModal()">
                                        <i class="fa fa-cog"></i>
                                        Tùy chọn cột
                                    </button>
                                @endif
                                <button type="button" class="btn btn-tool mt-1 pull-right" onclick="setItemTop()"
                                    data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fas fa-minus"></i></button>
                            </div>

                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body py-0" id="list-option-search">
                        <div class="row">
                            @foreach($searchDate as $key => $title)
                            <div class="col date-filter-box" data-key="{{ $key }}">
                                <div class="card card-secondary m-0">
                                    <div class="card-header p-1">
                                        <label for="{{$key}}_date_checkbox">
                                            <input name="{{$key}}_date_checkbox" {{ request()->input('date') == $key ? 'checked' : '' }} id="{{$key}}_date_checkbox" type="checkbox" value="" onclick="reloadList()"> {{ $title['title'] }}
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="{{ $title['tooltip'] }}">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="card-body p-2">
                                        <input name="{{$key}}_from_day" id="{{$key}}_from_date"
                                            class="form-control" type="text"
                                            value="{{ request()->input($key.'_from') ?: date('d/m/Y', strtotime("-3 days")) }}"
                                            data-bv-field="{{$key}}_from_date" onchange="reloadList()">
                                        <input name="{{$key}}_to_date" id="{{$key}}_to_date"
                                            class="form-control" type="text"
                                            value="{{ date("d/m/Y") }}"
                                            data-bv-field="{{$key}}_to_date" onchange="reloadList()">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="m-0"><strong>Trạng thái đơn hàng</strong>: <a href="javascript:;" onclick="checkAllStatus()">Chọn tất cả</a> | <a href="javascript:;" onclick="unCheckAllStatus()">Bỏ chọn tất cả</a></p>
                            </div>
                        </div>
                        <div class="row" style="pading: 5px; border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;overflow: auto;">
                            @foreach($filterStatuses as $index => $status)
                            <div class="col-2 p-1" style="font-size: 18px;border-left: 5px solid {{ $status->color }}; color:{{ $status->color }};">
                                <label class="mb-0 align-left" for="status_{{$status->id}}">
                                    <input name="status[]" type="checkbox" id="status_{{$status->id}}"
                                        @if((empty(request()->input('status_arr')) && $index==0) || (is_array(request()->input('status_arr'))&&in_array($status->id, request()->input('status_arr'))) ) checked @endif
                                        onclick="reloadList()" value="{{ $status->id }}" />
                                    {{ $status->level }}. {{$status->name}}</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="order-option mt-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <select name="source_id" id="source_id" class="w-auto d-inline ml-2 form-control"
                                        onchange="reloadList()">
                                        <option value="0">Chọn nguồn</option>
                                        @foreach($orderSources as $key => $source)
                                        <option value="{{$source->id}}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="shipping_service_id" id="shipping_service_id"
                                        class="w-auto d-inline ml-2 form-control" onchange="reloadList()">
                                        <option value="0">Vận chuyển</option>
                                        @foreach($deliveryMethods as $key => $method)
                                        <option value="{{$method->id}}">{{$method->name}}</option>
                                        @endforeach
                                    </select>
                                    <select name="order_type" id="order_type" class="w-auto d-inline ml-2 form-control"
                                        onchange="reloadList()">
                                        <option value="0">Loại đơn hàng</option>
                                        @foreach($order_types as $key => $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                    <select name="product_code" id="product_code" class="w-auto d-inline ml-2 form-control" onchange="reloadList()">
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($productList as $productCode => $productName)
                                            <option value="{{ $productCode }}">{{ $productName }} - {{ $productCode }}</option>
                                        @endforeach
                                    </select>
                                    <select name="bundle_id" id="bundle_id" class="w-auto d-inline ml-2 form-control"
                                        onchange="reloadList()">
                                        <option value="">Chọn phân loại</option>
                                        <option value="-1">Phân loại</option>
                                        <option value="-2">Đơn chưa phân loại</option>
                                        <option value="-3">Khách cũ</option>
                                        @foreach($productBundles as $key => $bundle)
                                        <option value="{{$bundle->id}}">{{$bundle->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="assigned_user_id" id="assigned_user_id"
                                                class="form-control select2" onchange="reloadList()">
                                                <option value="0">Tất cả sale</option>
                                                <option value="-1">Bỏ chia / Không chia</option>
                                                @foreach($sales as $key => $sale)
                                                <option value="{{$sale->id}}">{{$sale->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="marketing_id" id="marketing_id" class="form-control select2"
                                                onchange="reloadList()">
                                                <option value="0">Tất cả mkt</option>
                                                @foreach($marketings as $key => $marketing)
                                                <option value="{{$marketing->id}}">{{$marketing->name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="staff_id" id="staff_id" class="form-control select2"
                                                onchange="reloadList()">
                                                <option value="0">Tất cả NV</option>
                                                @foreach($users as $key => $user)
                                                <option value="{{$user->id}}">{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="row form-group mt-3">
                            <div class="col">
                                <a target="_blank" href="{{route('admin.sell.order.create')}}?close_when_done=1" class="btn  btn-info"><i
                                        class="fas fa-edit fa-spin"></i>Tạo đơn mới</a>
                                <button type="button" style="margin-left: 5px" class="btn  btn-warning text-white"
                                    onclick="showActionsModal()">
                                    Thao tác với đơn hàng được chọn
                                </button>
                                <button type="button" class="btn btn-danger  ml-2" onclick="checkAllAndShowActionModal()">
                                    Thao tác đơn hàng hiển thị
                                </button>
                                @if(getCurrentUser()->hasPermission('export_excel'))
                                <button type="button" style="margin-left: 5px" class="btn   bg-gradient-success"
                                    onclick="showExcelModal()">
                                    <i class="fa fa-download"></i> Excel
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-12 mt-3 p-2 table-list-max" id="table-list-order">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-compact nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab"
                                   data-toggle="pill"
                                   href="#nav-home"
                                   role="tab"
                                   aria-controls="nav-home"
                                   aria-selected="true">Danh sách đơn hàng</a>
                            </li>
                            @if (false)
                            @if(getCurrentUser()->hasPermission('quick_edit'))
                            <li class="nav-item">
                                <a class="nav-link text-warning" id="custom-tabs-three-profile-tab"
                                   href="{{route('admin.sell.order.quickEdit')}}"
                                   role="tab"
                                   aria-selected="false">Chỉnh sửa nhanh</a>
                            </li>
                            @endif
                            @if(getCurrentUser()->hasPermission('import_excel'))
                            <li class="nav-item">
                                <a class="nav-link text-dark font-weight-bold" id="custom-tabs-three-settings-tab"
                                   href="{{route('admin.sell.order.importExcel')}}"
                                   role="tab"
                                   aria-selected="false">Import Excel</a>
                            </li>
                            @endif
                            @if(getCurrentUser()->hasPermission('import_billWay'))
                            <li class="nav-item">
                                <a class="nav-link text-dark font-weight-bold" id="custom-tabs-three-settings-tab"
                                   href="{{route('admin.sell.order.importExcelBillWay')}}"
                                   role="tab"
                                   aria-selected="false">Import Excel Vận đơn</a>
                            </li>
                            @endif
                            @endif
                            @if(getCurrentUser()->hasPermission('import_billWay'))
                            <li class="nav-item">
                                <a class="nav-link text-dark font-weight-bold" id="custom-tabs-three-settings-tab"
                                   href="{{route('admin.sell.order.importExcelCollectMoney')}}"
                                   role="tab"
                                   aria-selected="false">Import Excel Thu tiền</a>
                            </li>
                            @endif
                            @if (false)
                            @if(getCurrentUser()->hasPermission('export_excel'))
                            <li class="nav-item">
                                <a class="nav-link" href="#" role="tab" aria-selected="false"><span onclick="exportTransportExcelOrders()"><i class="fa fa-car"></i> Xuất Excel vận chuyển</span></a>
                            </li>
                            @endif
                            @if(!getCurrentUser()->isAdmin() && getCurrentUser()->isOnlySale())
                            <li class="nav-item">
                                <a class="nav-link text-dark font-weight-bold" id="custom-tabs-three-settings-tab"
                                   href="{{route('admin.sell.order.take-care-again')}}"
                                   role="tab"
                                   aria-selected="false">Chăm sóc lại</a>
                            </li>
                            @endif
                            @endif
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                <table
                                    id="table-list"
                                    data-url="{{ url(route('admin.sell.order.getList')) }}"
                                    class="order-list-table table-list"
                                    data-toggle="table"
                                    data-toolbar="#keyword, #account_group_id, #customSearch,#count"
                                    data-search="false"
                                    data-show-toggle="false"
                                    data-show-columns="false"
                                    data-page-size="10"
                                    data-show-refresh="false"
                                    data-query-params="queryParamOrderList"
                                    data-pagination="true"
                                    data-side-pagination="server"
                                    data-sort-order="desc"
                                    data-show-pagination-switch="false"
                                    data-row-style="rowStyle"
                                >
                                    <thead>
                                        <tr>
                                            <th data-field="edit" data-formatter="actionFormat" class="text-center"></th>
                                            <th data-field="id" data-formatter="checkboxFormat">
                                                <input type="checkbox" onclick="checkAll(this)" id="checkAllOrder" class="check-all-order-item" />
                                            </th>
                                            @foreach ($sortDefault as $item)
                                                @if ($item->show)
                                                    <th data-field="{{ $item->name }}" data-formatter="{{ getFormatter($item->name) }}" {{ $item->sort ? 'data-sortable="true"' : ''}}>{{ $item->text }}</th>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="list-inline ml-2">
                                        <li class="list-inline-item">Tổng Số Đơn Hàng: <strong class="total-order"></strong></li>
                                        <li class="list-inline-item">Tổng tiền: <strong class="total-amount"></strong></li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    @include('admin.sell.order.status') @include('admin.sell.order.flash_share')
    @include('admin.sell.order.order_actions') @include('admin.sell.order.excel')
    @include('admin.sell.order.quick_edit')
    @include('admin.sell.order.order_sort')
    @include('admin.sell.order.order_history')
    @include('admin.sell.order.ems_confirm')
    @include('admin.sell.order.error_orders')
</section>
@stop
