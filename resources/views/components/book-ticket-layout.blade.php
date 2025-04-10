<!DOCTYPE html>
<html>
    <head>
        <title>{{$title}}</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js" ></script> <!-- Sửa lại đoạn script -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> <!-- Thêm biểu tượng giỏ hàng -->
        <style>
            /* Điều chỉnh màu nền cho trang */
            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh; /* Đảm bảo chiều cao của trang chiếm hết màn hình */
                background-color: #f8f9fa;  /* Màu nền sáng */
            }

            /* Định dạng thanh điều hướng */
            .navbar {
                background-color: #ff8fab;  /* Màu hồng nhẹ cho thanh navbar */
                font-weight: bold;
            }

            /* Các liên kết trong navbar */
            .navbar-nav .nav-link {
                color: white !important;  /* Đảm bảo liên kết luôn có màu trắng */
            }
            a {
                margin-right: 10px;  /* Thêm khoảng cách giữa các nút */
            }
            /* Định dạng phần tiêu đề */
            header {
                padding: 20px 0;  /* Thêm khoảng cách trên và dưới */
                background-color: #ffdae0;  /* Màu nền nhẹ cho header */
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

            /* Định dạng cho các phần tử trong nội dung */
            main {
                flex-grow: 1; /* Chiếm hết không gian còn lại giữa header và footer */
                padding: 20px 0;
            }
        </style>
    </head>
    <body>
        <header style='text-align:center'>
        <nav class="navbar navbar-light navbar-expand-sm">
                <div class='container-fluid p-0'>
                    <div class='col-9 p-0'>
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="{{url('bookticket')}}">Trang chủ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Tra cứu vé</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Lịch trình</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Liên hệ</a>
                                </li>
                            </ul>
                    </div>
                    @auth
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="dropdown" style="position: relative;">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" id="userDropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('account') }}">Quản lý</a>
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
        <!-- Banner -->
        <div class="text-center">
            <img src="{{ asset('images/banner.jpg') }}" style="width: 100%; object-fit: cover;">
        </div>
        <main style="width:1000px; margin:2px auto;">
            <div class='row'>
                <div class='col-12'>
                   {{$slot}}
                </div>
            </div>
        </main>
        <footer class="footer">
            © 2025 Bao Toan Car. All rights reserved.
            <br>Phát triển bởi Đội ngũ IT Bao Toan.
            <br><a href="#">Chính sách bảo mật</a> | <a href="#">Điều khoản sử dụng</a>
    </footer>
    </body>
</html>