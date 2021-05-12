<!DOCTYPE html>
<html>
<link>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="_token" content="{{ csrf_token() }}" />
<title>Đăng nhập | Shop manager</title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Font Awesome -->
<link rel="stylesheet" href="/theme/admin-lte/plugins/fontawesome-free/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- icheck bootstrap -->
<link rel="stylesheet" href="/theme/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="/theme/admin-lte/dist/css/adminlte.min.css">
<!-- ToastTr -->
<link rel="stylesheet" href="/theme/admin-lte/plugins/toastr/toastr.min.css">
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

<style type="text/css">
    .redirect-login {
        display: flex;
        justify-content: center;
        padding-top: 10px;
    }
    .login-logo,
    .register-logo {
        font-size: 1.5rem;
    }
    .login-page{
        background: url(/theme/admin-lte/dist/img/login-bg.jpg);
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }
</style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <p style="font-weight: bold;color: #fff;text-shadow: 2px 2px #000;">Chào mừng bạn đến với Vichat</p>
            <p style="color: #fff;text-shadow: 1px 1px #000;">Sự cố gắng của mình hôm nay sẽ cho kết quả tốt vào ngày mai</p>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">

                <!-- .login-form -->
                <form id="frm-login" action="user/login" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="account_id" class="form-control" placeholder="Tên tài khoản"
                            id="account_id" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" id="password"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="social-auth-links text-center mb-3">
                        <button type="button" class="btn btn-block btn-primary" id="btn-staff-login" onclick="login()">
                            Đăng nhập
                        </button>
                        <a href="{{ route('login') }}" class="redirect-login">Chuyển sang đăng nhập shop?</a>
                    </div>
                </form>
                <!-- /.login-form -->
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="/theme/admin-lte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/theme/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/theme/admin-lte/dist/js/adminlte.min.js"></script>
    <!-- ToastTr -->
    <script src="/theme/admin-lte/plugins/toastr/toastr.min.js"></script>
    <script>
        //Login
    var userLoginUrl = "{{route('superadmin.login.post')}}";
    function login(type) {
        let data = new FormData(jQuery("#frm-login")[0])
        $.ajax({
            url: userLoginUrl,
            type: "POST",
            data: data,
            contentType: false,
            processData: false,
            dataType:"JSON",
            success: function (response) {
                toastr.success(response.message);
                window.location.href = response.url
            }
        }).fail(function(error){
            toastr.error(error.responseJSON.message);
        })
    }
    </script>
</body>

</html>
