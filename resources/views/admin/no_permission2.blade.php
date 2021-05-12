<html><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Lockscreen</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('assets/release/css/layout.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  </head>
  <body class="hold-transition lockscreen">
  <!-- Automatic element centering -->
  <div class="lockscreen-wrapper text-center">
    <div class="lockscreen-logo">
      <a href="{{ route('admin.dashboard') }}"><b>Shop Manager</b></a>
    </div>
    <!-- User name -->
    <div class="lockscreen-name">Bạn không có quyền truy nhập vào trang này</div>

    <a type="btn btn-default button mt-4" href="{{ route('logout') }}">Đăng xuất</button>
    <!-- /.lockscreen-item -->
  </div>
  <!-- /.center -->
  <script src="{{ url('assets/release/js/layout.js') }}"></script>

  </body></html>
