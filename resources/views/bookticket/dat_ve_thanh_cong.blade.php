<x-book-ticket-layout> {{-- Sử dụng layout của bạn --}}
    <x-slot name="title">Đặt vé thành công</x-slot>

    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #d4edda; /* Màu nền xanh lá nhạt */
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 4em;
            color: #155724; /* Màu xanh lá đậm */
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 1.4em;
            color: #155724;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .booking-code {
            font-size: 1.1em;
            color: #0c5460; /* Màu xanh dương đậm */
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 10px 15px;
            border-radius: 4px;
            display: inline-block; /* Cho vừa đủ nội dung */
            margin-bottom: 25px;
        }
        .booking-code strong {
             font-size: 1.2em;
        }
        .next-steps a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .next-steps a:hover {
            background-color: #0056b3;
        }
         .note {
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
         }
    </style>

    <div class="success-container">
        {{-- Bạn có thể dùng icon FontAwesome hoặc SVG --}}
        <div class="success-icon">
            <i class="fas fa-check-circle"></i> {{-- Ví dụ FontAwesome --}}
            {{-- Hoặc dùng SVG: --}}
            {{-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="1em" height="1em"><path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM376.1 176.9L232.3 320.8c-6.4 6.4-15 9.6-23.6 9.6s-17.3-3.2-23.6-9.6L117.6 253c-13.1-13.1-13.1-34.2 0-47.3s34.2-13.1 47.3 0l43.7 43.7l119.9-119.9c13.1-13.1 34.2-13.1 47.3 0s13.1 34.2 0 47.3z"/></svg> --}}
        </div>

        <div class="success-message">
            {{ session('successMessage', 'Đặt vé thành công!') }}
        </div>

        @if(session('maPhieuDat'))
            <div class="booking-code">
                Mã đặt vé của bạn là: <strong>{{ session('maPhieuDat') }}</strong>
            </div>
        @endif

         <p class="note">
            Cảm ơn bạn đã đặt vé tại Bảo Toàn. Vui lòng kiểm tra email để xem chi tiết vé.
            Nhân viên sẽ liên hệ để xác nhận lại thông tin trước giờ khởi hành.
         </p>

        <div class="next-steps">
            <a href="{{ route('home') }}">Về trang chủ</a>
            {{-- Thêm link tra cứu vé nếu có --}}
            {{-- <a href="{{ route('lookup.ticket') }}">Tra cứu vé</a> --}}
        </div>
    </div>

</x-book-ticket-layout>