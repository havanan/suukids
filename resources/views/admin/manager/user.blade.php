@extends('layout.default')
@section('title') Admin | Quản lý tài khoản người dùng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Quản lý tài khoản người dùng'),
            'content' => [
                __('Quản lý tài khoản người dùng') => route('admin.manager.user')
            ],
            'active' => [__('Quản lý tài khoản người dùng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
  <script>
    $(document).ready(function(){
      $(".nav-tabs a").click(function(){
        $(this).tab('show');
      });
    });
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
                            <div class="text-center" style="float: right">
                              <button class="btn  btn-custom-warning ">+ Thêm</button>
                              <button class="btn btn-danger"><i class="fa fa-trash-alt"></i> Xóa</button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                              <table class="table none-border">
                                <tbody>
                                    <tr>
                                        <td width="20%">
                                            <input name="keyword" id="keyword" class="form-control" placeholder="Nhập tên tìm kiếm" type="text" value="">
                                        </td>
                                        <td width="20%">
                                            <select name="account_group_id" id="account_group_id" class="form-control">
                                                <option value="">Tất cả các nhóm</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-default">
                                                <i class="fa fa-search"></i> Tìm kiếm
                                            </button>
                                        </td>
                                        <td width="20%" class="text-left">
                                            Tổng: 1 tài khoản
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                              </table>
                            </div>
                            <div class="row">
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                  <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Tài khoản kích hoạt</a>
                                  <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Tài khoản chưa kích hoạt</a>
                                  
                                </div>
                            </div>
                            <div class="row">
                              <div class="tab-content w-100" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                  <table class="table table-bordered">
                                    <thead class="bg-secondary">
                                    <tr>
                                        <th style="width: 40px">
                                          <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1" checked>
                                            <label class="custom-control-label" for="customCheck1"></label>
                                          </div>
                                        </th>
                                        <th >Tên tài khoản</th>
                                        <th >Thông tin tài khoản</th>
                                        <th >Thời hạn</th>
                                        <th >Người tạo</th>
                                        <th >Quyền</th>
                                        <th>QL SHOP</th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                      <tr bgcolor="" valign="middle" id="UserAdmin_tr_hunter113">
                                        <td>
                                            1.
                                        </td>
                                        <td align="left">
                                            <strong>OFF <a href="#">hunter113</a></strong>
                                            <div class="small">(Group ID: <a href="index062019.php?page=admin_group_info&amp;group_id=12896">12896</a>/ hunter | Nhóm: Khách ngoài)</div>
                                            <span class="small">Online gần nhất: 22:18' 04/03/2020<br>tại IP: 2001:ee0:4141:73ce:5471:9063:bfff:f117</span>
                                        </td>
                                        <td align="left">
                                            <span class="text-bold">pham tuan</span>
                                            <br>
                                            <span class="small">Email: tuanpham.hunter@gmail.com</span>
                                            <br>
                                            <span class="small">Phone: 0982923545</span>
                                            <br>
                                            <span class="small">Tỉnh/thành: </span>
                                        </td>
                                        <td align="left">
                                          <div><span class="badge bg-success">Đã kích hoạt<br></div>
                                            <div class="small" style="margin-top: 5px;">Từ 02/03/20</div>
                                            <div class="small text-bold"> đến 09/03/20</div>
                                        </td>
                                        <td align="left">
                                          <div class="small"><br> lúc 22:23' 02/03/20</div>
                                        </td>
                                        <td align="left">
                                          <span class="badge bg-danger">Sở hữu</span>
                                        </td>
                                        <td align="center">
                                          [<span class="fas fa-check" aria-hidden="true"></span>]
                                          <br> <a href="index062019.php?page=user_admin&amp;cmd=edit&amp;id=hunter113" class="btn btn-warning btn-sm">Sửa</a>
                                        </td>
                                      </tr>
                                      <tr bgcolor="" valign="middle" id="UserAdmin_tr_hunter113">
                                        <td>
                                          <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck2" checked>
                                            <label class="custom-control-label" for="customCheck2"></label>
                                          </div>
                                          2
                                        </td>
                                        <td align="left">
                                            <strong>OFF <a href="#">hunter113</a> </strong>
                                            <div class="small">(Group ID: <a href="index062019.php?page=admin_group_info&amp;group_id=12896">12896</a>/ hunter | Nhóm: Khách ngoài)</div>
                                            <span class="small">Online gần nhất: 22:18' 04/03/2020<br>tại IP: 2001:ee0:4141:73ce:5471:9063:bfff:f117</span>
                                        </td>
                                        <td align="left">
                                            <span class="text-bold">pham tuan</span>
                                            <br>
                                            <span class="small">Email: tuanpham.hunter@gmail.com</span>
                                            <br>
                                            <span class="small">Phone: 0982923545</span>
                                            <br>
                                            <span class="small">Tỉnh/thành: </span>
                                        </td>
                                        <td align="left">
                                          <div><span class="badge bg-success">Đã kích hoạt<br></div>
                                            <div class="small" style="margin-top: 5px;">Từ 02/03/20</div>
                                            <div class="small text-bold"> đến 09/03/20</div>
                                        </td>
                                        <td align="left">
                                          <div class="small"><br> lúc 22:23' 02/03/20</div>
                                        </td>
                                        <td align="left">
                                          <span class="badge bg-secondary">Sale</span>
                                        </td>
                                        <td align="center">
                                          <br> <a href="index062019.php?page=user_admin&amp;cmd=edit&amp;id=hunter113" class="btn btn-warning btn-sm">Sửa</a>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                  <table class="table table-bordered">
                                    <thead class="bg-secondary">
                                    <tr>
                                        <th style="width: 40px">
                                          <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1" checked>
                                            <label class="custom-control-label" for="customCheck1"></label>
                                          </div>
                                        </th>
                                        <th >Tên tài khoản</th>
                                        <th >Thông tin tài khoản</th>
                                        <th >Thời hạn</th>
                                        <th >Người tạo</th>
                                        <th >Quyền</th>
                                        <th>QL SHOP</th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                      <tr bgcolor="" valign="middle" id="UserAdmin_tr_hunter113">
                                        <td>
                                          <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck2" checked>
                                            <label class="custom-control-label" for="customCheck2"></label>
                                          </div>
                                          1
                                        </td>
                                        <td align="left">
                                            <strong>OFF <a href="#">hunter113</a> </strong>
                                            <div class="small">(Group ID: <a href="index062019.php?page=admin_group_info&amp;group_id=12896">12896</a>/ hunter | Nhóm: Khách ngoài)</div>
                                            <span class="small">Online gần nhất: 22:18' 04/03/2020<br>tại IP: 2001:ee0:4141:73ce:5471:9063:bfff:f117</span>
                                        </td>
                                        <td align="left">
                                            <span class="text-bold">pham tuan</span>
                                            <br>
                                            <span class="small">Email: tuanpham.hunter@gmail.com</span>
                                            <br>
                                            <span class="small">Phone: 0982923545</span>
                                            <br>
                                            <span class="small">Tỉnh/thành: </span>
                                        </td>
                                        <td align="left">
                                          <div><span class="badge bg-dark">Chưa kích hoạt<br></div>
                                            <div class="small" style="margin-top: 5px;">Từ 02/03/20</div>
                                            <div class="small text-bold"> đến 09/03/20</div>
                                        </td>
                                        <td align="left">
                                          <div class="small"><br> lúc 22:23' 02/03/20</div>
                                        </td>
                                        <td align="left">
                                          <span class="badge bg-secondary">Sale</span>
                                        </td>
                                        <td align="center">
                                          <br> <a href="index062019.php?page=user_admin&amp;cmd=edit&amp;id=hunter113" class="btn btn-warning btn-sm">Sửa</a>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
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
