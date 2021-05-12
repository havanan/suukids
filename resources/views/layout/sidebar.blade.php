<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('admin.sell.order.index')}}" class="brand-link">
        <img src="/theme/admin-lte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Shop Manager</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2" id="nav">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

                <li class="nav-item">
                    <a href="{{route('admin.dashboard')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if(getCurrentUser()->isAdmin())
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-shopping-cart"></i>
                        <p>
                            Cấu hình
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('admin.config.ems.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cấu hình kho và dịch vụ</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('admin.action_log.list')}}" class="nav-link">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>
                                    Quản lý lịch sử tìm kiếm
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('admin.login_log.list')}}" class="nav-link">
                                <i class="nav-icon fas fa-user-clock"></i>
                                <p>
                                    Quản lý lịch sử đăng nhập
                                </p>
                            </a>
                        </li>
                    </ul>
                    @if (getCurrentUser()->shipping_partner  == 'vtp')
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('admin.config.vtpost.index')}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cấu hình ViettelPost</p>
                                </a>
                            </li>
                        </ul>
                    @elseif (getCurrentUser()->shipping_partner  == 'ems')
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('admin.config.ems.viewSaveToken')}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cấu hình EMS</p>
                                </a>
                            </li>
                        </ul>
                    @endif
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('admin.config.cloudfone.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Cấu hình Cloudfone</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-shopping-cart"></i>
                        <p>
                            Bán hàng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('admin.customer.overview')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Chăm sóc khách hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.sell.order.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Quản lý đơn hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/cost" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Quản lý chi phí MKT</p>
                            </a>
                        </li>
                        @if(getCurrentUser()->isAdmin())
                        <li class="nav-item">
                            <a href="{{route('admin.shop.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Quản lý Shop</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.order_source.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Quản lý nguồn đơn hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.status.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quản lý trạng thái</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.delivery.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quản lý hình thức giao hàng</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @if(getCurrentUser()->hasPermission('manager_customer'))
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Khách hàng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('admin.customer.create')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tạo mới</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.customer.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh sách</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.customer.group.list')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Phân loại khách hàng</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if(getCurrentUser()->isAdmin())
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Người dùng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('admin.user.create')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tạo mới</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.user.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>QL tài khoản người dùng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.user_group.index')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quản lý nhóm người dùng</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if(getCurrentUser()->isStockManager())
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Kho
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('admin.stock.stock_in_import')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nhập kho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.stock.stock_in_list')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh sách phiếu nhập kho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.stock.stock_out_import')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Xuất Kho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.stock.stock_out_list')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh sách phiếu xuất kho</p>
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="{{route('admin.stock.ems_config')}}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cấu hình EMS</p>
                            </a>
                        </li>
                        {{--
                        <li class="nav-item">
                            <a href="{{route('stock.stock_out.manager')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Quản lý nhập kho</p>
                        </a>
                </li> --}}


                <li class="nav-item">
                    <a href="{{route('admin.supplier.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Danh sách nhà cung cấp</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.stock.warehouse.list')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Khai báo kho</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('admin.stock.product')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Tồn kho</p>
                    </a>
                </li>
            </ul>
            </li>
            @endif
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-user"></i>
                    <p>
                        Hồ sơ
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{route('admin.user.profile')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Trang cá nhân</p>
                        </a>
                    </li>
                    @if(getCurrentUser()->isAdmin())
                    <li class="nav-item">
                        <a href="{{route('admin.profile.permission.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Quản lý quyền</p>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{route('admin.export_logs.excel')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Lịch sử xuất Excel</p>
                        </a>
                    </li>
                </ul>
            </li>
            @if (getCurrentUser()->isAdmin())
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fa fa-th-large"></i>
                    <p>
                        Sản phẩm
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{route('admin.product.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Danh sách</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.manager.products')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Danh sách (Sửa nhanh)</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.bundle.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Phân loại</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('admin.unit.index')}}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Đơn vị</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif()
            <li class="nav-item">
                <a href="{{route('admin.report.index')}}" class="nav-link">
                    <i class="nav-icon fa fa-sign"></i>
                    <p>
                        Báo cáo
                    </p>
                </a>
            </li>
                {{-- <li class="nav-item">
                    <a href="{{route('admin.introduce.index')}}" class="nav-link">
                        <i class="nav-icon fa fa-info-circle"></i>
                        <p>
                            Giới thiệu
                        </p>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
