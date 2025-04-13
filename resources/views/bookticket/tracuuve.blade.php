<x-book-ticket-layout> 
    <x-slot name="title">Tra cứu vé</x-slot>

    <div class="container mt-5">
        <h2 class="text-center mb-4">TRA CỨU THÔNG TIN ĐẶT VÉ</h2>

        <form method="POST" action="{{ route('tracuuve') }}" class="text-center mb-5">
            @csrf
            <input type="text" name="sodienthoai" placeholder="Vui lòng nhập số điện thoại" value="{{ old('sodienthoai') }}" required><br>
            <input type="text" name="mave" placeholder="Vui lòng nhập mã vé" value="{{ old('mave') }}" required><br>
            <button type="submit">Tra cứu</button>
        </form>

        @if(session('error'))
            <div class="error text-danger font-weight-bold mt-3 text-center">{{ session('error') }}</div>
        @endif

        @if(isset($ticket))
        <div class="ticket-wrapper d-flex justify-content-center">
            <div class="ticket-card">
                <div class="ticket-header text-center">
                    <h3>THÔNG TIN VÉ XE</h3>
                    <small><strong>Mã vé:</strong> {{ $ticket->MaPhieuDat }}</small>
                </div>

                <div class="ticket-body row">
                    <div class="col-md-6 ticket-section">
                        <p><strong>👤 Họ tên:</strong> {{ $ticket->HoTen }}</p>
                        <p><strong>📞 Số điện thoại:</strong> {{ $ticket->SoDienThoai }}</p>
                        <p><strong>💺 Số ghế:</strong> {{ $ticket->SoGhe }}</p>
                        <p><strong>💲 Tổng tiền:</strong> {{ number_format($ticket->TongSoTien, 0, ',', '.') }} VNĐ</p>
                    </div>
                    <div class="col-md-6 ticket-section">
                        <p><strong>🗺️ Lộ trình:</strong> {{ $ticket->TenLoTrinh }}</p>
                        <p><strong>🕒 Khởi hành:</strong> {{ $ticket->ThoiGianKhoiHanh }}</p>
                    </div>
                </div>

                <div class="ticket-footer text-center mt-4">
                    <em>🎫 Vui lòng mang mã vé khi lên xe và đến trước giờ khởi hành.</em>
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600&display=swap');

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f0f4f8;
        }

        input {
            padding: 12px;
            margin: 10px;
            width: 500px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #ff4e1d;
            outline: none;
        }

        button {
            padding: 12px 30px;
            background-color: #ffefea;
            color: #ff4e1d;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #ffcdcc;
        }

        .ticket-wrapper {
            padding: 20px;
        }

        .ticket-card {
            width: 100%;
            max-width: 850px;
            background: linear-gradient(135deg, #fff0f3, #ffe3e8);
            border: 2px solid #ffc2d1;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(255, 143, 171, 0.3);
            padding: 30px 40px;
        }

        .ticket-header h3 {
            color: #ff4e1d;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .ticket-header small {
            font-size: 16px;
            color: #555;
        }

        .ticket-body {
            margin-top: 30px;
        }

        .ticket-section {
            padding: 10px 20px;
        }

        .ticket-section p {
            font-size: 17px;
            margin-bottom: 16px;
            color: #333;
        }

        .ticket-footer {
            font-size: 17px;
            color: #a94442;
            background-color: #fff6f7;
            padding: 12px;
            border-radius: 12px;
            border: 1px dashed #ffa4b5;
        }
    </style>
</x-book-ticket-layout>
