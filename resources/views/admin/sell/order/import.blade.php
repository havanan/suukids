@extends('layout.default')
@section('title') Admin | Import đơn hàng từ excel (Marketing) @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Import đơn hàng từ excel (Marketing)'),
            'content' => [
                __('Import đơn hàng từ excel (Marketing)') => route('admin.sell.order.importExcel')
            ],
            'active' => [__('Import đơn hàng từ excel (Marketing)')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <link rel="stylesheet" href="{{ url('css/source.css') }}">
    @include('layout.flash_message')
    <script>
        let uploadImportExcelUrl = "{{ route('admin.sell.order.uploadImportExcel') }}"
        let importExcelUrl = "{{ route('admin.sell.order.importExcel.post') }}"

        function uploadImportExel(element) {
            if (!$('#excel_file')[0] || !$('#excel_file')[0].files || $('#excel_file')[0].files.length <= 0) {
                alert('Vui lòng chọn một file');
                return;
            }

            var fd = new FormData();
            var files = $('#excel_file')[0].files[0];
            fd.append('excel_file',files);

            $.ajax({
                url: uploadImportExcelUrl,
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

        function importExcel() {
            if (!$('#status_id').val()) {
                alert('Vui lòng nhập trạng thái');
                return;
            }

            $.ajax({
                url: importExcelUrl,
                type: 'post',
                data: {
                    status_id: $('#status_id').val(),
                    assign_user_id: $('#assign_user_id').val()
                },
                dataType:"JSON",
                success: function(response){
                    toastr.success(response.message);
                },
            }).fail(function (e) {
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
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-6">
                                    <div class="row text-right">
                                        <select id="status_id" class="form-control col-md-3" name="status_id">
                                            <option value="">Chọn trạng thái</option>
                                            @foreach($statuses as $key => $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-1 ml-1 pt-2">|</span>
                                        @if(!getCurrentUser()->isOnlyMarketing())
                                        <select id="assign_user_id" class="form-control col-md-4" name="assign_user_id">
                                            <option value="">Chọn nhân viên gán đơn</option>
                                            @foreach($sales as $key => $sale)
                                            <option value="{{ $sale->id }}">{{ $sale->account_id }}</option>
                                            @endforeach
                                        </select>
                                        @endif
                                        <button class="btn btn-primary col-md-3 mr-2 ml-2" onclick="importExcel()">Thực hiện import</button>
                                        <div class="col-md-1 text-right">
                                        <small class="badge badge-secondary mt-2">(Bước 2)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
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
                                        <a target="_blank" href="{{asset('theme/admin-lte/dist/file/order_import_excel1.xlsx')}}"
                                        class="btn btn-default ">
                                            <i class="fa fa-angle-double-down"></i>
                                            Tải Excel mẫu <span class="label label-default">(Cập nhật: Ngày 09/05/2019)</span>
                                        </a>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon no-border">Chọn file excel</span>
                                            <input name="excel_file" id="excel_file" class="form-control ml-2" type="file" value="">
                                            <span class="input-group-btn"></span>
                                            <input name="upload" id="upload" class="btn btn-warning ml-2" value="Tải excel lên hệ thống" onclick="uploadImportExel($(this))" type="button">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        .<small class="badge badge-secondary mt-2">(Bước 1)</small>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-warning-custom">
                                    Bước 1: Tải file excel đã được <strong>chỉnh thứ tự cột theo file mẫu</strong> (rất quan trọng)<br>
                                    Bước 2: Nhấn <strong>Thực hiện import</strong> để hoàn thành.<br>
                                </div>
                                <hr>
                                <div class="badge badge-secondary">* Mới cập nhật: cột Facebook page, Facebook post và Facebook của khách hàng, Nguồn đơn hàng</div>
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
