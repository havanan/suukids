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
                <li class="nav-item">
                    <a href="{{route('superadmin.shop.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>
                            Quản lý cửa hàng
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('superadmin.action_log.list')}}" class="nav-link">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>
                            Quản lý lịch sử tìm kiếm
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('superadmin.login_log.list')}}" class="nav-link">
                        <i class="nav-icon fas fa-user-clock"></i>
                        <p>
                            Quản lý lịch sử đăng nhập
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('superadmin.shop.create')}}" class="nav-link">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>
                            Thêm mới cửa hàng
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('superadmin.report.shop')}}" class="nav-link">
                        <i class="nav-icon fa fa-sign"></i>
                        <p>
                            Doanh thu Shop
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route('superadmin.report.product')}}" class="nav-link">
                        <i class="nav-icon fa fa-sign"></i>
                        <p>
                            Doanh thu Sản phẩm
                        </p>
                    </a>
                </li>

                 <li class="nav-item">
                    <a href="{{route('superadmin.profile.change_password')}}" class="nav-link">
                        <i class="nav-icon fa fa-key"></i>
                        <p>
                            Đổi mật khẩu
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
