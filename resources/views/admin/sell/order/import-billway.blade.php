@extends('layout.default')
@section('title') Admin | Import vận đơn từ excel (Marketing) @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Import vận đơn từ excel (Marketing)'),
            'content' => [
                __('Import vận đơn từ excel (Marketing)') => route('admin.sell.order.importExcelBillWay')
            ],
            'active' => [__('Import vận đơn từ excel (Marketing)')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    @include('layout.flash_message')
    <script>
        let importExcelUrl = "{{ route('admin.sell.order.importExcelBillWay.post') }}"

        function importExcel() {
            if (!$('#excel_file')[0] || !$('#excel_file')[0].files || $('#excel_file')[0].files.length <= 0) {
                alert('Vui lòng chọn một file');
                return;
            }

            var fd = new FormData();
            var files = $('#excel_file')[0].files[0];
            fd.append('excel_file',files);

            $.ajax({
                url: importExcelUrl,
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                dataType:"JSON",
                success: function(response){
                    toastr.success('Upload thành công');
                    $('#uploads-rows-count').html(response.rows);
                },
            }).fail(function (e) {
                console.log(e);
                toastr.error(e.responseJSON.message);
            });
        }
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <hr>
                                <div class="alrert alert-success" style="padding:20px;">
                                    Có <strong style="font-size:30px" id="uploads-rows-count">0</strong> dòng của excel đã tải - Tối đa 4000 dòng/file.
                                    <span class="label label-warning">* File excel phải có trình tự cột giống file mẫu.</span>
                                </div>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a target="_blank" href="{{asset('theme/admin-lte/dist/file/order_import_excel_billway.xlsx')}}"
                                        class="btn btn-default ">
                                            <i class="fa fa-angle-double-down"></i>
                                            Tải Excel mẫu <span class="label label-default">(Cập nhật: Ngày 09/05/2019)</span>
                                        </a>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-addon no-border">Chọn file excel</span>
                                            <input name="excel_file" id="excel_file" class="form-control ml-2" type="file" value="">
                                            <span class="input-group-btn"></span>
                                            <input name="upload" id="upload" class="btn btn-warning ml-2" value="Thực hiện import" onclick="importExcel()" type="button">
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
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop
