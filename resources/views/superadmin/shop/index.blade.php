@extends('layout.superadmin.default')
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
  <script>
    $(document).on("click",".btn-delete-shop",function(){
        var id = $(this).data("id");
        Swal.fire({
            title: "Cảnh báo !",
            text: "Bạn chắc chắn muốn xóa ?",
            type: "warning",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonColor: "#007bff",
            confirmButtonText: "Xóa",
            cancelButtonText: "Hủy",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('superadmin.shop.delete') }}",
                    dataType: "JSON",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status === "OK") {
                            alertPopup("success", "Xóa thành công");
                            setTimeout(function(){ window.location.reload(); }, 1000);
                        } else {
                            alertPopup("error", "Xóa thất bại");
                        }
                    },
                    error: function(e) {
                        alertPopup("error", "Xóa thất bại");
                    }
                });
            }
        });
    })
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
                                <div class="col-md-12 mb-3">
                                    <div class="text-right">
                                        <a href="{{route('superadmin.shop.create')}}" class="btn btn-success">
                                            <i class="fa fa-plus mr-2"></i> Thêm mới
                                        </a>
                                    </div>
                                </div>
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
                                        <th>Chủ cửa hàng</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Sửa</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($shops as $key => $shop)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td>
                                                {{ $shop->name }}
                                            </td>
                                            <td>
                                                {{ $shop->address }}
                                            </td>
                                            <td>
                                                {{ $shop->phone }}
                                            </td>
                                            <td>
                                               @if(!empty($shop->owner)) {{$shop->owner->account_id}} @endif
                                            </td>
                                            <td>
                                                {!! $shop->expired_date && strtotime($shop->expired_date) <= strtotime("now") ? "<span class='text-danger'> Đã hết hạn: ".date("d/m/Y",strot)."</span>" : $shop->expired_date !!}
                                            </td>
                                            <td>
                                                {!! !empty($shop->is_pause)
                                                ? "<span class='text-danger'>Tạm dừng</span>" : "<span class='text-success'>Đang hoạt động</span>" !!}
                                            </td>
                                            <td>
                                                <a href="{{ route('superadmin.shop.edit', $shop->id) }}" class="btn btn-default btn-sm edit-note ml-3">
                                                    <i class="far fa-edit"></i> Cập nhật
                                                </a>

                                                <a href="{{ route('superadmin.shop.shopLogin', $shop->id) }}" class="btn btn-primary btn-sm edit-note ml-3" target="_blank">
                                                    Đăng nhập <i class="fas fa-long-arrow-alt-right"></i>
                                                </a>

                                                <a class="btn btn-danger btn-delete-shop btn-sm ml-3" href="javascript:void(0);" data-id="{{ $shop->id }}"><i class="fas fa-trash-alt"></i> Xóa</a>
                                            </td>
                                        </tr>
                                    @endforeach
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
    @include('layout.flash_message')
@stop
