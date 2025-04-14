{{-- resources/views/bookticket/dat-ve-thanh-cong.blade.php --}}
<x-book-ticket-layout>
    <x-slot name="title">Đặt vé thành công</x-slot>

    <style>
        .success-card { border: 1px solid #198754; border-left-width: 5px; }
        .detail-row { margin-bottom: 0.5rem; }
        .detail-label { font-weight: 600; min-width: 150px; display: inline-block; }
    </style>

    <div class="container mt-5">
        <div class="card success-card shadow-sm">
            <div class="card-body text-center">
                <h4 class="card-title text-success mb-3"><i class="fas fa-check-circle"></i> Đặt vé thành công!</h4>
                <p class="card-text">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của Bao Toan Car.</p>
                <p>Thông tin chi tiết vé của bạn đã được ghi nhận.</p>
                {{-- Hiển thị chi tiết vé nếu có --}}
                @if($bookingDetails)
                    <hr>
                    <div class="text-start mt-4 mb-3 mx-auto" style="max-width: 500px;">
                        <h5>Chi tiết vé: #{{ $bookingDetails->MaPhieuDat }}</h5>
                        <div class="detail-row"><span class="detail-label">Họ tên:</span> {{ $bookingDetails->TenKhachHang }}</div>
                        <div class="detail-row"><span class="detail-label">Số điện thoại:</span> {{ $bookingDetails->SoDienThoai }}</div>
                        <div class="detail-row"><span class="detail-label">Email:</span> {{ $bookingDetails->Email }}</div>
                        <div class="detail-row"><span class="detail-label">Tuyến:</span> {{ $bookingDetails->TenLoTrinh }}</div>
                        <div class="detail-row"><span class="detail-label">Khởi hành:</span> {{ \Carbon\Carbon::parse($bookingDetails->ThoiGianKhoiHanh)->format('H:i - d/m/Y') }}</div>
                        <div class="detail-row"><span class="detail-label">Ghế đã chọn:</span> {{ $bookingDetails->DanhSachTenGhe }}</div>
                        <div class="detail-row"><span class="detail-label">Điểm đón:</span> {{ $bookingDetails->TenDiemDon ?? 'N/A' }} {{ $bookingDetails->GhiChuDiemDon ? '('.$bookingDetails->GhiChuDiemDon.')' : '' }}</div>
                        <div class="detail-row"><span class="detail-label">Điểm trả:</span> {{ $bookingDetails->TenDiemTra ?? 'N/A' }} {{ $bookingDetails->GhiChuDiemTra ? '('.$bookingDetails->GhiChuDiemTra.')' : '' }}</div>
                        <div class="detail-row"><span class="detail-label">Tổng tiền:</span> <strong>{{ number_format($bookingDetails->TongSoTien, 0, ',', '.') }} VNĐ</strong></div>
                        <div class="detail-row"><span class="detail-label">Thanh toán:</span> <span class="badge bg-{{ $bookingDetails->TrangThaiThanhToan == 'DaThanhToan' ? 'success' : 'warning' }}">{{ $bookingDetails->TrangThaiThanhToan ?? 'N/A' }}</span></div>
                        {{-- <div class="detail-row"><span class="detail-label">Hình thức:</span> {{ $bookingDetails->HinhThucThanhToan ?? 'N/A' }}</div> --}}
                    </div>
                    <hr>
                @endif
                <p class="mb-0">Vui lòng kiểm tra email (bao gồm cả thư mục Spam) để xem lại thông tin hoặc <a href="#">liên hệ</a> chúng tôi nếu cần hỗ trợ.</p>
            </div>
             <div class="card-footer bg-transparent border-0 text-center pb-3">
                  <a href="{{ route('home') }}" class="btn btn-primary">
                     <i class="fas fa-home"></i> Quay về Trang chủ
                 </a>
                 {{-- Thêm nút tra cứu vé nếu có chức năng đó --}}
                 {{-- <a href="{{ route('tra-cuu-ve') }}" class="btn btn-info ms-2">
                     <i class="fas fa-search"></i> Tra cứu vé
                 </a> --}}
             </div>
        </div>


    </div>

</x-book-ticket-layout>