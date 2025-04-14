<x-book-ticket-layout> {{-- Sử dụng layout của bạn --}}
    <x-slot name="title">Xác nhận đặt vé</x-slot>

    <style>
        .confirmation-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .details-section, .payment-section {
            background-color: #fff;
            padding: 25px;
            border-radius: 5px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }
        .section-title {
            font-size: 1.5em;
            color: #0056b3;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-item label {
            font-weight: bold;
            color: #495057;
            flex-basis: 40%; /* Phân bổ không gian cho label */
        }
        .info-item span {
            color: #212529;
            text-align: right;
            flex-basis: 58%; /* Phân bổ không gian cho value */
        }
        .total-amount span {
            font-weight: bold;
            color: #dc3545;
            font-size: 1.2em;
        }
        .payment-method {
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .payment-method p { margin: 5px 0; }

        .action-buttons {
            display: flex;
            justify-content: space-between; /* Đặt nút ở hai đầu */
            margin-top: 30px;
        }
        .action-buttons button, .action-buttons a {
            padding: 12px 25px;
            font-size: 1.1em;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-confirm {
            background-color: #28a745; /* Màu xanh lá */
            color: white;
            border: none;
        }
        .btn-confirm:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #6c757d; /* Màu xám */
            color: white;
            border: none;
            text-align: center;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>

    <div class="confirmation-container">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Phần Chi tiết vé xe --}}
        <div class="details-section">
            <h2 class="section-title">Chi tiết vé xe</h2>

            <div class="info-item">
                <label>Tuyến đường:</label>
                <span>{{ $bookingData['tenLoTrinh'] }}</span>
            </div>
            <div class="info-item">
                <label>Thời gian khởi hành:</label>
                <span>{{ \Carbon\Carbon::parse($bookingData['thoiGianKhoiHanh'])->format('H:i - d/m/Y') }}</span>
            </div>
             <hr style="margin: 15px 0;">
            <div class="info-item">
                <label>Họ và tên:</label>
                <span>{{ $bookingData['hoTen'] }}</span>
            </div>
            <div class="info-item">
                <label>Số điện thoại:</label>
                <span>{{ $bookingData['soDienThoai'] }}</span>
            </div>
            <div class="info-item">
                <label>Email:</label>
                <span>{{ $bookingData['email'] }}</span>
            </div>
             <hr style="margin: 15px 0;">
            <div class="info-item">
                <label>Điểm đón:</label>
                <span>{{ $bookingData['tenDiemDon'] }}</span>
            </div>
            <div class="info-item">
                <label>Điểm trả:</label>
                <span>{{ $bookingData['tenDiemTra'] }}</span>
            </div>
            @if(!empty($bookingData['ghiChu']))
            <div class="info-item">
                <label>Ghi chú:</label>
                <span>{{ $bookingData['ghiChu'] }}</span>
            </div>
            @endif
             <hr style="margin: 15px 0;">
            <div class="info-item">
                <label>Số ghế đã chọn:</label>
                <span>{{ count($bookingData['maXeGheIds']) }}</span>
            </div>
            <div class="info-item">
                <label>Vị trí ghế:</label>
                <span>{{ $bookingData['seatCodesDisplay'] }}</span>
            </div>
            <div class="info-item total-amount">
                <label>Tổng tiền:</label>
                <span>{{ number_format($bookingData['tongTien'], 0, ',', '.') }} VNĐ</span>
            </div>
        </div>

        {{-- Phần Phương thức thanh toán --}}
        <div class="payment-section">
            <h2 class="section-title">Phương thức thanh toán</h2>
            <div class="payment-method">
                <p><strong><i class="fas fa-money-bill-wave"></i> Thanh toán tại nhà xe</strong></p>
                <p>Vui lòng thanh toán tại điểm đón:</p>
                <p><strong>{{ $bookingData['tenDiemDon'] }}</strong></p>
                <p><small>(Nhân viên sẽ liên hệ xác nhận trước giờ khởi hành)</small></p>
            </div>
        </div>

        {{-- Các nút hành động --}}
        <div class="action-buttons">
            {{-- Nút Quay lại --}}
            <button type="button" class="btn-back" onclick="window.history.back();">Quay lại</button>

            {{-- Nút Đặt vé (Submit form ẩn) --}}
            <form action="{{ route('bookticket.finalize') }}" method="POST" style="display: inline;">
                @csrf
                {{-- Không cần gửi lại dữ liệu ở đây vì đã lấy từ session trong controller finalize --}}
                <button type="submit" class="btn-confirm">Đặt vé</button>
            </form>
        </div>

    </div>
</x-book-ticket-layout>