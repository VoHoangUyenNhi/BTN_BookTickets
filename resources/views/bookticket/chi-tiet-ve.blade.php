{{-- resources/views/bookticket/chi-tiet-ve.blade.php --}}
<x-book-ticket-layout>
    <x-slot name="title">Thông tin đặt vé</x-slot>


    {{-- CSS nếu cần thêm --}}
    <style>
        .summary-section, .customer-section, .pickup-section {
             border: 1px solid #eee; padding: 20px; margin-bottom: 20px; border-radius: 5px; background-color: #f9f9f9;
         }
        .error-message { color: red; font-size: 0.9em; }
        label { font-weight: bold; }
        .form-group { margin-bottom: 1rem; }
        .info-row { margin-bottom: 8px; }
        .info-label { font-weight: bold; min-width: 110px; display: inline-block;}
        /* Sticky sidebar */
        @media (min-width: 768px) {
            .sticky-top-md { position: sticky; top: 20px; z-index: 1020; }
        }
    </style>


    <div class="container mt-4">


         {{-- Hiển thị lỗi chung từ server (vd: lỗi hệ thống) --}}
        @if($errors->has('booking_error'))
            <div class="alert alert-danger">{{ $errors->first('booking_error') }}</div>
        @endif
        @if($errors->has('session_error'))
            <div class="alert alert-warning">{{ $errors->first('session_error') }} <a href="{{ route('home') }}">Quay lại tìm kiếm</a></div>
        @endif




        {{-- Chỉ hiển thị form nếu không có lỗi session --}}
        @if(!$errors->has('session_error'))
        <form action="{{ route('dat-ve.xac-nhan') }}" method="POST" id="customerInfoForm" novalidate> {{-- novalidate để dùng validation của Bootstrap --}}
            @csrf
            {{-- Dữ liệu vé (maChuyenDi, seats, total) lấy từ session ở Controller --}}


            <div class="row">
                {{-- Cột Thông tin khách hàng và Điểm đón/trả --}}
                <div class="col-md-7 col-lg-8 order-md-first"> {{-- Đảo thứ tự trên mobile --}}
                    {{-- THÔNG TIN KHÁCH HÀNG --}}
                    <div class="customer-section">
                        <h2>THÔNG TIN KHÁCH HÀNG</h2>
                        <div class="form-group">
                            <label for="name">Họ và tên <span class="text-danger">*</span></label>
                            {{-- old('name', Auth::user()->name ?? '') : giữ lại giá trị cũ hoặc lấy tên user nếu đăng nhập --}}
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nhập họ và tên" value="{{ old('name', Auth::user()->name ?? '') }}" required>
                            {{-- Hiển thị lỗi validation cho trường 'name' --}}
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                            {{-- old('phone', Auth::user()->phone ?? '') : giữ lại giá trị cũ hoặc lấy sđt user nếu có --}}
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Nhập số điện thoại" value="{{ old('phone', Auth::user()->phone ?? '') }}" required pattern="^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Nhập email" value="{{ old('email', Auth::user()->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check">
                            {{-- old('terms') ? 'checked' : '' : giữ lại trạng thái checked nếu có lỗi validation --}}
                            <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với <a href="#" target="_blank">Điều khoản sử dụng</a> và <a href="#" target="_blank">Chính sách bảo mật</a>. <span class="text-danger">*</span>
                            </label>
                             @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div> {{-- d-block để lỗi checkbox hiển thị đúng --}}
                            @enderror
                        </div>
                    </div>


                    {{-- THÔNG TIN ĐÓN TRẢ --}}
                    <div class="pickup-section mt-3">
                        <h2>THÔNG TIN ĐÓN TRẢ</h2>
                         {{-- Điểm đón --}}
                        <div class="form-group">
                            <label for="pickup_point">Điểm đón khách <span class="text-danger">*</span></label>
                            <select class="form-select @error('pickup_point') is-invalid @enderror" id="pickup_point" name="pickup_point" required>
                                <option value="">-- Chọn điểm đón --</option>
                                @foreach ($pickupPoints as $point)
                                    {{-- old('pickup_point') == $point->MaDiemDonTra ? 'selected' : '' : giữ lại điểm đã chọn nếu có lỗi --}}
                                    <option value="{{ $point->MaDiemDonTra }}" {{ old('pickup_point') == $point->MaDiemDonTra ? 'selected' : '' }}>
                                        {{ $point->TenDiem }}
                                    </option>
                                @endforeach
                            </select>
                             @error('pickup_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                             {{-- Ghi chú điểm đón (tùy chọn) --}}
                            <div class="mt-2">
                                 <label for="pickup_notes" class="form-label visually-hidden">Ghi chú điểm đón</label> {{-- Ẩn label nếu không cần thiết --}}
                                 <input type="text" class="form-control @error('pickup_notes') is-invalid @enderror" id="pickup_notes" name="pickup_notes" value="{{ old('pickup_notes') }}" placeholder="Ghi chú thêm cho điểm đón (vd: số nhà, địa chỉ cụ thể nếu cần trung chuyển)">
                                 @error('pickup_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                 @enderror
                            </div>
                        </div>


                         {{-- Điểm trả --}}
                        <div class="form-group">
                            <label for="dropoff_point">Điểm trả khách <span class="text-danger">*</span></label>
                            <select class="form-select @error('dropoff_point') is-invalid @enderror" id="dropoff_point" name="dropoff_point" required>
                                <option value="">-- Chọn điểm trả --</option>
                                @foreach ($dropoffPoints as $point)
                                     <option value="{{ $point->MaDiemDonTra }}" {{ old('dropoff_point') == $point->MaDiemDonTra ? 'selected' : '' }}>
                                        {{ $point->TenDiem }}
                                    </option>
                                @endforeach
                            </select>
                             @error('dropoff_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                             {{-- Ghi chú điểm trả (tùy chọn) --}}
                             <div class="mt-2">
                                 <label for="dropoff_notes" class="form-label visually-hidden">Ghi chú điểm trả</label>
                                 <input type="text" class="form-control @error('dropoff_notes') is-invalid @enderror" id="dropoff_notes" name="dropoff_notes" value="{{ old('dropoff_notes') }}" placeholder="Ghi chú thêm cho điểm trả">
                                 @error('dropoff_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                 @enderror
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Cột Tóm tắt vé và Thanh toán --}}
                <div class="col-md-5 col-lg-4 order-md-last">
                    <div class="summary-section sticky-top-md">
                         <h2>TÓM TẮT VÉ</h2>
                         <div class="info-row">
                             <span class="info-label">Tuyến:</span>
                             <span class="info-content">{{ $trip->TenLoTrinh ?? 'N/A' }}</span>
                         </div>
                         <div class="info-row">
                             <span class="info-label">Khởi hành:</span>
                             <span class="info-content">{{ isset($trip->ThoiGianKhoiHanh) ? \Carbon\Carbon::parse($trip->ThoiGianKhoiHanh)->format('H:i - d/m/Y') : 'N/A' }}</span>
                         </div>
                         <div class="info-row">
                            <span class="info-label">Loại xe:</span>
                            <span class="info-content">{{ $trip->TenLoaiXe ?? 'N/A' }}</span>
                        </div>
                         <div class="info-row">
                             <span class="info-label">Ghế đã chọn:</span>
                             <span class="info-content">{{ $bookingData['selectedSeatNames'] ?? 'N/A' }}</span>
                         </div>
                         <div class="info-row">
                             <span class="info-label">Số lượng vé:</span>
                             <span class="info-content">{{ $bookingData['seatCount'] ?? 0 }}</span>
                         </div>
                         <hr>
                         <div class="info-row">
                            <span class="info-label">Giá vé:</span>
                            <span class="info-content">{{ number_format($bookingData['giaVe'] ?? 0, 0, ',', '.') }} VNĐ/vé</span>
                        </div>
                         <div class="info-row">
                             <span class="info-label">Tổng tiền:</span>
                             <strong class="info-content" id="totalPrice">{{ number_format($bookingData['totalAmount'] ?? 0, 0, ',', '.') }} VNĐ</strong>
                         </div>
                         <hr>


                         {{-- Có thể thêm lựa chọn thanh toán ở đây --}}
                         {{-- <div class="form-group">
                             <label for="payment_method">Phương thức thanh toán:</label>
                             <select name="payment_method" id="payment_method" class="form-select">
                                 <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Thanh toán khi lên xe</option>
                                 <option value="vnpay" {{ old('payment_method') == 'vnpay' ? 'selected' : '' }}>Thanh toán qua VNPAY</option>
                                  Thêm các phương thức khác
                             </select>
                             @error('payment_method') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                         </div> --}}


                        <button type="submit" class="btn btn-primary w-100 mt-3">Xác nhận đặt vé</button>
                        {{-- Link quay lại phải dùng MaChuyenDi từ session --}}
                         <a href="{{ route('dat-ve.chon-ghe', ['maChuyenDi' => $bookingData['maChuyenDi'] ?? 0]) }}" class="btn btn-outline-secondary w-100 mt-2">Quay lại chọn ghế</a>
                    </div>
                </div>
            </div>
        </form>
        @endif {{-- End if !$errors->has('session_error') --}}
    </div>


    {{-- Script cho validation phía client của Bootstrap (tùy chọn) --}}
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('#customerInfoForm')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
            })
        })()
    </script>


</x-book-ticket-layout>