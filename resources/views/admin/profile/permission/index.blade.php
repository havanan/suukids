@extends('layout.default')
@section('title') Admin | Danh sách nhóm quyền @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Danh sách nhóm quyền'),
            'content' => [
                __('Danh sách nhóm quyền') => route('admin.profile.permission.index')
            ],
            'active' => [__('Danh sách nhóm quyền')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    <link rel="stylesheet" href="{{asset('theme/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">

    <script src="{{asset('theme/admin-lte/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('theme/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script>
        $("#table").DataTable({
            "ordering": false,
            searching: false,
            bLengthChange : false,
            language: {
                "sProcessing":   "Đang xử lý...",
                "sLengthMenu":   "Xem _MENU_ mục",
                "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
                "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
                "sInfoFiltered": "(được lọc từ _MAX_ mục)",
                "sInfoPostFix":  "",
                "sSearch":       "Tìm:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "Đầu",
                    "sPrevious": "Trước",
                    "sNext":     "Tiếp",
                    "sLast":     "Cuối"
                }
            }
        });

        jQuery('#delete-btn').on('click', function() {
            let deleteUrl = "{{route('admin.profile.permission.delete')}}"

            Swal.fire({
	         title: "Bạn có chắc chắn muốn xóa",
	         type: "warning",
	         showCancelButton: true,
	         confirmButtonClass: 'btn-danger',
	         confirmButtonText: "Chắc chắn",
	         cancelButtonText: "Không"
	       }).then(function(isConfirm){
		         if (isConfirm.value){
		        	var ids = jQuery('input[name="delete[]"]:checked').map(function(){
                        return jQuery(this).val()
                    }).get();

                    jQuery.ajax({
                        url:deleteUrl,
                        cache:false,
                        type:"DELETE",
                        dataType:"JSON",
                        data:{"_token":token,"data":ids},
                        success:function(){
                            toastr.success("Xóa thành công");
                            window.location.reload();
                        }
		   		    }).fail(function(){
		     			toastr.error("Có lỗi xảy ra, thử lại sau");
		     		})
		         }
	       })
        });

        //Check All
        let checkAll = jQuery('#checkAll');
        checkAll.on('click', function() {
            if (isCheckAll()) {
                jQuery('input[name="delete[]"]').prop('checked', false);
            } else {
                jQuery('input[name="delete[]"]').prop('checked', true);
            }
        });

        jQuery('input[name="delete[]"]').on('click', function() {
            checkAll.prop('checked', isCheckAll())
        });

        function isCheckAll() {
            return jQuery('input[name="delete[]"]:not(:checked)').length == 0;
        }
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
                                        <a href="{{route('admin.profile.permission.create')}}" class="btn btn-primary"><i
                                                    class="fa fa-plus mr-2"></i> Thêm mới</a>
                                        <button class="btn btn-danger" id="delete-btn"><i class="fa fa-trash-alt mr-2"></i>Xóa</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="w-40 text-center">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="checkAll">
                                            <label for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th>Tên</th>
                                    <th>Quyền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($permissions as $index => $permission)
                                    <tr>
                                        <td class="text-center">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" name="delete[]" value="{{$permission->id}}"  id="checkbox-{{$index}}">
                                                <label for="checkbox-{{$index}}"></label>
                                            </div>
                                        </td>
                                        <td class="text-left">
                                            {{ $permission->name }}
                                        </td>
                                        <td class="text-left">
                                            {{ $permission->permissions_title }}
                                        </td>
                                        <td class="text-left w-50">
                                            @foreach ($permission->status_permissions_info as $key => $info)
                                                <div style="margin-right:5px;margin-bottom:2px;border:1px solid #ccc;display:inline-block;padding:2px 3px;">
                                                    <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background-color:{{$info["color"]}}"></span>
                                                    {{$info["name"]}}
                                                    <span style="border:1px solid #CCC;font-size:10px;padding:2px;background:{{$info["permission_color"]}}">{{$info["permission"]}}</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('admin.profile.permission.edit', $permission->id)}}" class="btn btn-warning btn-sm">Sửa</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
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
