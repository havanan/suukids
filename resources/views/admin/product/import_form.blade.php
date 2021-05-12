<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import sản phẩm từ Excel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{route('admin.product.importExcel')}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="p-5 text-center">----------------Chọn file Excel-----------------</div>

                    File mẫu: <a title="Tải về file mẫu" href="{{asset('theme/admin-lte/dist/file/excel_sp_mau.xlsx')}}"
                                 target="_blank">
                        <img src="{{asset('theme/admin-lte/dist/img/file_excel_sp_mau.png')}}"width="90%" alt="">
                    </a>
                    <hr>
                    <div class="form-group">
                        <input name="excel_file"
                               id="excel_file"
                               class="form-control"
                               required type="file"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  />
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
