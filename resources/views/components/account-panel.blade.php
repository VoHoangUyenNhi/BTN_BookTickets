<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin tài khoản</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Scripts -->

  <style>
    /* Điều chỉnh màu nền cho trang */
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh; /* Đảm bảo chiều cao của trang chiếm hết màn hình */
      background-color: #f8f9fa;  /* Màu nền sáng */
      }
      header {
                padding: 20px 0;  /* Thêm khoảng cách trên và dưới */
                background-color: #ffdae0;  /* Màu nền nhẹ cho header */
            }
    .navbar {
        background-color: #ff8fab;  /* Màu hồng nhẹ cho thanh navbar */
        font-weight:bold;
        margin:0 auto;
        margin-bottom: 20px;
     

    }
    .navbar-nav
    {
        margin:0 auto;
        width:1000px;
        
    }
    .navbar-nav a
    {
        color:black!important;
    }
    .content {
      margin-left: 240px; /* Width of sidebar */
    }
    /* Định dạng footer */
    footer {
      background-color: #ff8fab;
      color: white;
      padding: 20px 0;
      text-align: center;
      margin-top: auto;  /* Đảm bảo footer luôn ở dưới cùng */
    }

    footer a {
      color: white;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
    main {
                flex-grow: 1; /* Chiếm hết không gian còn lại giữa header và footer */
                padding: 20px 0;
            }
  </style>
</head>
<body>
  <header>
  <nav class="navbar">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="{{url('/')}}">Trang chủ</a>
            </li>
        </ul>
        @auth
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="dropdown" style="position: relative;">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" id="userDropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profileinfo') }}">Hồ sơ</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">Đăng xuất</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                        <a href="{{ route('login') }}">
                            <button class='btn btn-sm btn-primary'>Đăng nhập</button>
                        </a>&nbsp;
                        <a href="{{ route('register') }}">
                            <button class='btn btn-sm btn-success'>Đăng ký</button>
                        </a>
                    @endauth
  </nav>
  </header>
  <main role="main">
    <div class="container"> <!-- Đây là phần căn giữa nội dung -->
      {{ $slot }}
    </div>
  </main>
    </div>
  </div>
<footer class="footer">
            © 2025 Bao Toan Car. All rights reserved.
            <br>Phát triển bởi Đội ngũ IT Bao Toan.
            <br><a href="#">Chính sách bảo mật</a> | <a href="#">Điều khoản sử dụng</a>
    </footer>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
