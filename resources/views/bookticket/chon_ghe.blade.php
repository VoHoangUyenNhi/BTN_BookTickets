{{-- File: resources/views/bookticket/chon_ghe.blade.php --}}
<x-book-ticket-layout> {{-- Hoặc layout bạn đang dùng --}}
    <x-slot name="title">Chọn ghế - {{ $tripInfo->TenLoTrinh }}</x-slot>

    {{-- Thêm CSS nội tuyến hoặc link đến file CSS của bạn --}}
    <style>
        /* === SAO CHÉP CSS TỪ FILE style.css CỦA BẠN VÀO ĐÂY === */
        /* Hoặc tốt hơn là link đến file CSS dùng chung */
        .container { display: flex; flex-wrap: wrap; gap: 20px; margin: 20px auto; max-width: 1200px; }
        .seating-area { border: 1px solid #ccc; padding: 15px; border-radius: 5px; width: 100%; background-color: #f9f9f9; margin-bottom: 15px;}
        .seating-area h4 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    text-align: center; /* Căn giữa tiêu đề */
    font-size: 1.1em; /* Có thể tăng kích thước chữ nếu muốn */
    color: #333;
    margin-bottom: 15px;
}
    .seats {
        display: grid;
        /* === THAY ĐỔI: Định nghĩa lưới 5 cột === */
        grid-template-columns: repeat(5, 1fr); /* Chỉ 5 cột */
        gap: 10px; /* Khoảng cách giữa các ghế (có thể điều chỉnh) */
        padding: 10px;
        max-width: 450px; /* Giảm max-width cho phù hợp 5 cột (tùy chỉnh) */
        margin: 0 auto; /* Căn giữa khu vực ghế */
    }   
    .seat {
        cursor: pointer; /* <<< QUAN TRỌNG: Đặt con trỏ mặc định là pointer */
        position: relative; /* Cần cho định vị ::before */
        overflow: hidden; /* Giấu phần thừa của icon nếu có */
        border: 1px solid #adb5bd; /* Viền xám nhạt mặc định */
        background-color: #ffffff; /* Nền trắng mặc định (ghế trống) */
        color: #343a40; /* Chữ đen mặc định */
        padding: 8px 5px;
        text-align: center;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.9em;
        transition: background-color 0.2s, color 0.2s, transform 0.1s;
        min-width: 50px;
        
    }
    .seat.taken {
        background-color: #dc3545;  /* ===> MÀU NỀN ĐỎ <=== */
        color: white;              /* Chữ trắng cho dễ đọc trên nền đỏ */
        border-color: #c82333;   /* Viền đỏ đậm hơn */
        cursor: not-allowed;
        font-weight: normal; /* Chữ không cần đậm */
    }
    .seat.selected {
        background-color: #007bff;
        color: white;
        border-color: #0056b3;
        transform: scale(1.05);
    }
    .seat:not(.selected):not(.taken) {
     border-color: #007bff; /* Viền xanh cho ghế trống */
     /* cursor: pointer; <- Không cần lặp lại, đã có ở .seat */
    }   

    .seat:not(.taken):not(.selected):hover {
        background-color: #e9ecef;  /* Nền xám rất nhạt */
        color: #adb5bd;        /* Chữ xám nhạt */
        border-color: #dee2e6;   /* Viền xám nhạt hơn */
        font-weight: normal; /* Chữ không cần đậm */
    }
    

    .legend { margin-top: 20px; display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
    .legend-item { display: flex; align-items: center; gap: 5px; }
    .legend-box { width: 20px; height: 20px; border: 1px solid #ccc; }
    .legend-box.empty { background-color: #ffffff; border: 1px solid #007bff; } /* Trống */
    .legend-box.selected { background-color: #007bff; border-color: #0056b3; } /* Đang chọn */
    .legend-box.taken { background-color:rgb(214, 28, 41); border-color: #dee2e6; } /* Đã bán */


    .bottom-section { display: flex; flex-wrap: wrap; width: 100%; gap: 20px; }
    .left-section, .right-section { flex: 1; min-width: 300px; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);}
    .section { margin-bottom: 25px; }
    .section h2 { font-size: 1.2em; color: #0056b3; border-bottom: 2px solid #0056b3; padding-bottom: 8px; margin-bottom: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group select { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; }
    .form-group input[type="checkbox"] { margin-right: 5px; }
    .radio-group label { margin-right: 15px; }
    .info-row { margin-bottom: 10px; display: flex; justify-content: space-between; }
    .info-label { font-weight: bold; color: #555; }
    .info-content { text-align: right; color: #333; }
    .error-message { color: red; font-size: 0.9em; }
    button[type="submit"], button[type="button"] {
        background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; margin-right: 10px; transition: background-color 0.3s;
    }
    button[type="submit"]:hover { background-color: #0056b3; }
    #cancelBtn { background-color: #6c757d; }
    #cancelBtn:hover { background-color: #5a6268; }
    p.total { font-size: 1.1em; margin: 10px 0; }
    p.total span { font-weight: bold; color: #dc3545; }
    #totalPrice { font-size: 1.3em; }
        
    </style>

<div class="container">
    <h3>CHỌN GHẾ NGỒI CHO CHUYẾN: {{ $tripInfo->TenLoTrinh }} ({{ \Carbon\Carbon::parse($tripInfo->ThoiGianKhoiHanh)->format('H:i d/m/Y') }})</h3>
    <p>Xe: {{ $tripInfo->TenLoaiXe }} - Biển số: {{ $tripInfo->BienSoXe ?? 'N/A' }}</p>

    <!-- GHE TANG DUOI -->
    @if($lowerDeckSeats->isNotEmpty())
    <div class="seating-area">
        <h4>Ghế tầng dưới</h4> {{-- Tiêu đề sẽ được căn giữa bởi CSS --}}
        <div class="seats" id="lowerDeck">
            {{-- Sắp xếp tự nhiên cho tất cả ghế tầng dưới --}}
            @foreach ($lowerDeckSeats->sortBy('MaGhe', SORT_NATURAL) as $seat)
                <div class="seat {{ $seat->TrangThai === 'da_ban' ? 'taken' : '' }}"
                     data-seat="{{ $seat->MaGhe }}"
                     data-ma-xe-ghe="{{ $seat->MaXeGhe }}"
                     onclick="selectSeat('{{ $seat->MaGhe }}')">
                    {{ $seat->MaGhe }}
                </div>
            @endforeach
        </div>
    </div>
    @endif
    {{-- Kết thúc tầng dưới --}}


    <!-- GHE TANG TREN -->
    @if($upperDeckSeats->isNotEmpty())
    <div class="seating-area">
        <h4>Ghế tầng trên</h4> {{-- Tiêu đề sẽ được căn giữa bởi CSS --}}
        <div class="seats" id="upperDeck">
             {{-- Sắp xếp tự nhiên cho tất cả ghế tầng trên --}}
             @foreach ($upperDeckSeats->sortBy('MaGhe', SORT_NATURAL) as $seat)
                <div class="seat {{ $seat->TrangThai === 'da_ban' ? 'taken' : '' }}"
                     data-seat="{{ $seat->MaGhe }}"
                     data-ma-xe-ghe="{{ $seat->MaXeGhe }}"
                     onclick="selectSeat('{{ $seat->MaGhe }}')">
                    {{ $seat->MaGhe }}
                </div>
            @endforeach
        </div>
    </div>
    @endif
    {{-- Kết thúc tầng trên --}}


    <!-- CHU THICH -->
    {{-- Phần chú thích giữ nguyên --}}
    <div class="legend">
        <div class="legend-item">
            <div class="legend-box empty"></div>
            <span>Ghế trống</span>
        </div>
        <div class="legend-item">
            <div class="legend-box selected"></div>
            <span>Ghế đang chọn</span>
        </div>
        <div class="legend-item">
            <div class="legend-box taken"></div>
            <span>Ghế đã bán</span>
        </div>
    </div>

        {{-- Form sẽ bao gồm cả phần thông tin KH và thông tin lộ trình/giá --}}
        {{-- Action sẽ trỏ đến route xử lý đặt vé (sẽ tạo sau) --}}

        <form action="{{ route('bookticket.process_booking') }}" method="POST" id="bookingForm" onsubmit="return validateForm()">
        @csrf {{-- Token chống CSRF của Laravel --}}
            <input type="hidden" name="maChuyenDi" value="{{ $tripInfo->MaChuyenDi }}">
            <input type="hidden" name="selectedSeats" id="selectedSeatsInput" value=""> {{-- Sẽ chứa MaXeGhe --}}
            <input type="hidden" name="totalAmount" id="totalAmountInput" value="0">
            <input type="hidden" name="giaVe" value="{{ $giaApDung }}"> {{-- Giá vé đơn vị --}}

            @if ($errors->any())
        <div class="alert alert-danger" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px auto; max-width: 800px;">
            <strong style="font-weight: bold;">Có lỗi xảy ra:</strong>
            <ul style="margin-top: 10px; margin-bottom: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

            <div class="bottom-section">
                <!-- THONG TIN KHACH HANG -->
                <div class="left-section">
                    <div class="section">
                        <h2>THÔNG TIN KHÁCH HÀNG</h2>
                        {{-- Lấy thông tin user nếu đã đăng nhập --}}
                        @auth
                            <p>Đặt vé với tài khoản: <strong>{{ Auth::user()->name ?? Auth::user()->email }}</strong></p>
                            <input type="hidden" name="name" value="{{ Auth::user()->name ?? '' }}">
                            <input type="hidden" name="phone" value="{{ Auth::user()->phone ?? '' }}"> {{-- Giả sử bảng users có cột phone --}}
                            <input type="hidden" name="email" value="{{ Auth::user()->email ?? '' }}">
                             <div class="form-group">
                                <label for="phone_display">Số điện thoại liên hệ</label>
                                <input type="text" id="phone_display" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}" placeholder="Nhập số điện thoại liên hệ" required>
                                <span id="phoneError" class="error-message" style="display: none;">Số điện thoại không hợp lệ!</span>
                             </div>
                             <div class="form-group">
                                <label for="email_display">Email liên hệ</label>
                                <input type="email" id="email_display" name="email" value="{{ old('email', Auth::user()->email ?? '') }}" placeholder="Nhập email liên hệ" required>
                                <span id="emailError" class="error-message" style="display: none;">Email không hợp lệ!</span>
                            </div>
                        @else
                            {{-- Nếu chưa đăng nhập thì yêu cầu nhập --}}
                            <div class="form-group">
                                <label for="name">Họ và tên</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" required>
                                <span id="fullNameError" class="error-message" style="display: none;">Vui lòng nhập Họ và Tên.</span>
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại" required>
                                <span id="phoneError" class="error-message" style="display: none;">Số điện thoại không hợp lệ!</span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email" required>
                                <span id="emailError" class="error-message" style="display: none;">Email không hợp lệ!</span>
                            </div>
                        @endauth
                        <div class="form-group">
                        <input type="checkbox" id="terms" name="terms" value="1" @if(old('terms')) checked @endif required>
                        <label for="terms">Chấp nhận <a href="#" target="_blank">Điều khoản</a> và <a href="#" target="_blank">Chính sách bảo mật</a></label>
                             <span id="termsError" class="error-message" style="display: none;">Bạn phải đồng ý với điều khoản.</span>
                        </div>
                    </div>
                    <!-- THONG TIN DON TRA -->
                    <div class="section">
                    <h2>THÔNG TIN ĐÓN TRẢ</h2>
                        <div class="form-group">
                            <label for="pickup">ĐIỂM ĐÓN KHÁCH</label>
                            {{-- <div class="radio-group">
                                <input type="radio" id="pickup-point" name="pickup_type" value="point" checked onclick="togglePickupInput()">
                                <label for="pickup-point">Điểm đón</label>
                                <input type="radio" id="pickup-transfer" name="pickup_type" value="transfer" onclick="togglePickupInput()">
                                <label for="pickup-transfer">Trung chuyển</label>
                            </div> --}}
                            <select id="pickup" name="pickup" required>
                                <option value="">-- Chọn điểm đón --</option>
                                @foreach ($pickupPoints as $point)
                                <option value="{{ $point->MaDiemDonTra }}" 
                                @if(old('pickup') == $point->MaDiemDonTra) selected @endif>
                                {{ $point->TenDiem }}
                        </option>
                                @endforeach
                            </select>
                            <span id="pickupError" class="error-message" style="display: none;">Vui lòng chọn điểm đón.</span>
                            {{-- Input cho trung chuyển (nếu cần) --}}
                            {{-- <div id="pickup-transfer-container" style="display: none;">
                                <label for="pickup-transfer-input">Nhập địa chỉ đón trung chuyển</label>
                                <input type="text" id="pickup-transfer-input" name="pickup_transfer" placeholder="Số nhà, tên đường, phường/xã..." />
                            </div> --}}
                        </div>

                        <div class="form-group">
                            <label for="dropoff">ĐIỂM TRẢ KHÁCH</label>
                            {{-- <div class="radio-group">
                                <input type="radio" id="dropoff-point" name="dropoff_type" value="point" checked onclick="toggleDropoffInput()">
                                <label for="dropoff-point">Điểm trả</label>
                                <input type="radio" id="dropoff-transfer" name="dropoff_type" value="transfer" onclick="toggleDropoffInput()">
                                <label for="dropoff-transfer">Trung chuyển</label>
                            </div> --}}
                            <select id="dropoff" name="dropoff" required>
                                <option value="">-- Chọn điểm trả --</option>
                                @foreach ($dropoffPoints as $point)
                                <option value="{{ $point->MaDiemDonTra }}" @if(old('dropoff') == $point->MaDiemDonTra) selected @endif>
                            {{ $point->TenDiem }}
                        </option>
                            @endforeach
                            </select>
                             <span id="dropoffError" class="error-message" style="display: none;">Vui lòng chọn điểm trả.</span>
                            {{-- Input cho trung chuyển (nếu cần) --}}
                            {{-- <div id="dropoff-transfer-container" style="display: none;">
                                <label for="dropoff-transfer-input">Nhập địa chỉ trả trung chuyển</label>
                                <input type="text" id="dropoff-transfer-input" name="dropoff_transfer" placeholder="Số nhà, tên đường, phường/xã..." />
                            </div> --}}
                        </div>
                         <div class="form-group">
                            <label for="notes">Ghi chú (tùy chọn)</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Ví dụ: đón ở cổng số 2, mặc áo màu đỏ...">{{ old('notes') }}</textarea>
                            </div>
                    </div>
                </div>

                <!-- THONG TIN LO TRINH & GIA -->
                <div class="right-section">
                    <div class="section">
                        <h2>THÔNG TIN LỘ TRÌNH</h2>
                        <div class="info-row">
                            <span class="info-label">Tuyến đường:</span>
                            <span class="info-content">{{ $tripInfo->TenLoTrinh }}</span>
                        </div>
                         <div class="info-row">
                            <span class="info-label">Thời gian khởi hành:</span>
                            <span class="info-content">{{ \Carbon\Carbon::parse($tripInfo->ThoiGianKhoiHanh)->format('H:i - d/m/Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Thời gian di chuyển:</span>
                            <span class="info-content">{{ date('H', strtotime($tripInfo->ThoiGianDiChuyen)) }} giờ {{ date('i', strtotime($tripInfo->ThoiGianDiChuyen)) }} phút</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label" >Vị trí ghế đã chọn:</span>
                            <span class="info-content" id="selectedSeatsDisplay">Chưa chọn</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label" >Số vé:</span>
                            <span class="info-content" id="seatCountDisplay">0</span>
                        </div>
                    </div>
                    <!-- CHI TIET GIA -->
                    <div class="section">
                        <h2>CHI TIẾT GIÁ</h2>
                         <div class="info-row">
                            <span class="info-label">Giá vé:</span>
                            <span class="info-content">{{ number_format($giaApDung, 0, ',', '.') }} VNĐ/vé</span>
                         </div>
                         <hr>
                         <div class="info-row" style="font-size: 1.3em; font-weight: bold;">
                            <span class="info-label">Tổng tiền:</span>
                            <span class="info-content" id="totalPriceDisplay" style="color: #dc3545;">0 VNĐ</span>
                        </div>
                        <br>
                        <button type="submit">Tiếp tục</button> {{-- Nút này sẽ submit form --}}
                        <button type="button" id="cancelBtn" onclick="cancelSelection()">Hủy chọn</button>
                    </div>
                </div>
            </div>
        </form> {{-- Đóng form --}}
    </div>

    {{-- Bao gồm JavaScript --}}
    <script>
        let selectedSeats = []; // Mảng chứa các object { id: maXeGhe, code: seatCode }
const giaVe = {{$giaApDung}};

// Hàm chọn/bỏ chọn ghế (Đảm bảo bạn đang dùng phiên bản này)
function selectSeat(seatCode) {
    // Tìm phần tử DOM dựa trên mã ghế (A1, B2...)
    const seatElement = document.querySelector(`.seat[data-seat='${seatCode}']`);
    if (!seatElement) return; // Không tìm thấy ghế

    // Lấy MaXeGhe từ data attribute
    const maXeGhe = seatElement.getAttribute('data-ma-xe-ghe');
    if (!maXeGhe) {
        console.error("Lỗi: Không tìm thấy data-ma-xe-ghe cho ghế:", seatCode);
        return;
    }

    // *** KIỂM TRA GHẾ ĐÃ BÁN ***
    if (seatElement.classList.contains('taken')) {
        // Không làm gì cả hoặc chỉ hiển thị thông báo nhỏ nếu muốn
        // alert('Ghế [' + seatCode + '] đã có người đặt.');
        return; // Ngăn chặn hành động tiếp theo
    }

    // *** TOGGLE CLASS 'selected' ***
    // Thêm hoặc xóa class 'selected' khỏi phần tử ghế
    seatElement.classList.toggle('selected');

    // *** CẬP NHẬT MẢNG selectedSeats ***
    if (seatElement.classList.contains('selected')) {
        // Nếu ghế VỪA ĐƯỢC chọn (class 'selected' được thêm vào)
        // Thêm thông tin ghế vào mảng (nếu chưa có)
        if (!selectedSeats.some(seat => seat.id === maXeGhe)) {
            selectedSeats.push({ id: maXeGhe, code: seatCode });
        }
    } else {
        // Nếu ghế VỪA BỊ BỎ CHỌN (class 'selected' được xóa đi)
        // Loại bỏ ghế khỏi mảng
        selectedSeats = selectedSeats.filter(seat => seat.id !== maXeGhe);
    }

    // Cập nhật giao diện (tổng tiền, danh sách ghế, số lượng)
    updateUI();
}

// Hàm cập nhật giao diện (Giữ nguyên như trước)
function updateUI() {
    let seatCount = selectedSeats.length;
    let seatCodes = selectedSeats.map(seat => seat.code).sort((a, b) => { // Sắp xếp mã ghế hiển thị
         const numA = parseInt(a.substring(1));
         const numB = parseInt(b.substring(1));
         if (a.charAt(0) === b.charAt(0)) {
            return numA - numB;
         }
         return a.charAt(0) < b.charAt(0) ? -1 : 1;
      }).join(', ');

    document.getElementById('selectedSeatsDisplay').innerText = seatCount > 0 ? seatCodes : 'Chưa chọn';
    document.getElementById('seatCountDisplay').innerText = seatCount;
    document.getElementById('selectedSeatsInput').value = selectedSeats.map(seat => seat.id).join(','); // Lưu MaXeGhe

    const total = seatCount * giaVe;
    document.getElementById('totalPriceDisplay').innerText = total.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('totalAmountInput').value = total;
}

// Hàm hủy chọn (Giữ nguyên)
function cancelSelection() {
    selectedSeats.forEach(seat => {
        const seatElement = document.querySelector(`.seat[data-ma-xe-ghe='${seat.id}']`);
        if (seatElement) {
            seatElement.classList.remove('selected');
        }
    });
    selectedSeats = [];
    updateUI();
}

// Hàm validateForm (Giữ nguyên)
function validateForm() {
    // ... logic kiểm tra form ...
    let isValid = true;
    // Reset error messages
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');

    // 1. Kiểm tra ghế đã chọn
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất một ghế.');
        isValid = false;
    }
     // ... các kiểm tra khác (thông tin KH, điểm đón/trả, điều khoản) ...
     /*@guest
         const nameInput = document.getElementById('name');
         const phoneInput = document.getElementById('phone');
         const emailInput = document.getElementById('email');
         if (!nameInput.value.trim()) { document.getElementById('fullNameError').style.display = 'inline'; isValid = false; }
         const phonePattern = /^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/;
         if (!phonePattern.test(phoneInput.value)) { document.getElementById('phoneError').style.display = 'inline'; isValid = false; }
         const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         if (!emailPattern.test(emailInput.value)) { document.getElementById('emailError').style.display = 'inline'; isValid = false; }
     @else
         const phoneDisplayInput = document.getElementById('phone_display');
         const emailDisplayInput = document.getElementById('email_display');
         const phonePattern = /^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/;
         if (!phonePattern.test(phoneDisplayInput.value)) { document.getElementById('phoneError').style.display = 'inline'; isValid = false; }
         const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         if (!emailPattern.test(emailDisplayInput.value)) { document.getElementById('emailError').style.display = 'inline'; isValid = false; }
         if (isValid) {
            document.querySelector('input[name="phone"]').value = phoneDisplayInput.value;
            document.querySelector('input[name="email"]').value = emailDisplayInput.value;
         }
     @endguest */
     const pickupSelect = document.getElementById('pickup');
     const dropoffSelect = document.getElementById('dropoff');
     if (pickupSelect.value === "") { document.getElementById('pickupError').style.display = 'inline'; isValid = false; }
     if (dropoffSelect.value === "") { document.getElementById('dropoffError').style.display = 'inline'; isValid = false; }
     const termsCheckbox = document.getElementById('terms');
     if (!termsCheckbox.checked) { document.getElementById('termsError').style.display = 'inline'; isValid = false; }

    if (!isValid) {
        alert('Vui lòng kiểm tra lại các thông tin được đánh dấu đỏ.');
    }
    return isValid;
}

// Thêm sắp xếp vào updateUI để danh sách ghế hiển thị luôn đúng thứ tự
// (Đã thêm vào hàm updateUI ở trên)

</script>
    </script>

</x-book-ticket-layout>