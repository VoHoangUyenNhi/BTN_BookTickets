{{-- resources/views/bookticket/chon-ghe.blade.php --}}
<x-book-ticket-layout>
    <x-slot name="title">Chọn ghế ngồi</x-slot>

    {{-- CSS cho ghế (nên đưa vào file CSS riêng) --}}
    <style>
        .seating-area { margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; border-radius: 5px; }
        .seats { display: flex; flex-wrap: wrap; gap: 10px; padding-left: 0; list-style: none; }
        .seat {
            border: 1px solid #007bff; padding: 8px; min-width: 45px; text-align: center;
            cursor: pointer; border-radius: 3px; background-color: #e7f3ff;
            color: #007bff; font-weight: bold; user-select: none; transition: all 0.2s;
            display: inline-flex; align-items: center; justify-content: center;
            line-height: 1.2; /* Đảm bảo text canh giữa */
        }
        .seat.taken { background-color: #6c757d; color: white; cursor: not-allowed; border-color: #6c757d; }
        .seat.selected { background-color: #28a745; color: white; border-color: #28a745; }
        .legend { display: flex; gap: 20px; margin-top: 15px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 5px; }
        .legend-box { width: 20px; height: 20px; border: 1px solid #ccc; border-radius: 3px; display: inline-block; }
        .legend-box.empty { background-color: #e7f3ff; border-color: #007bff; }
        .legend-box.taken { background-color: #6c757d; border-color: #6c757d; }
        .legend-box.selected { background-color: #28a745; border-color: #28a745; }
        .info-row { margin-bottom: 8px; }
        .info-label { font-weight: bold; min-width: 120px; display: inline-block;}
        #totalPrice { font-size: 1.2em; color: #c00; }
        .error-message { color: red; font-size: 0.9em; margin-top: 5px; display: block; }
        .summary-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; background-color: #f8f9fa; }
        /* Sticky sidebar */
        @media (min-width: 768px) {
            .sticky-top-md { position: sticky; top: 20px; z-index: 1020; }
        }
    </style>

    <div class="container mt-4">

        {{-- Hiển thị lỗi chung (ví dụ: ghế không còn trống sau khi submit) --}}
        @if($errors->has('seat_error'))
            <div class="alert alert-danger">{{ $errors->first('seat_error') }}</div>
        @endif
        @if($errors->has('error'))
            <div class="alert alert-danger">{{ $errors->first('error') }}</div>
        @endif


        <div class="row">
            {{-- Cột Chọn Ghế --}}
            <div class="col-md-7 col-lg-8">
                <h3>CHỌN GHẾ NGỒI</h3>

                {{-- GHẾ TẦNG DƯỚI --}}
                @if($lowerDeckSeats->isNotEmpty())
                <div class="seating-area">
                    <h4>Ghế tầng dưới</h4>
                    <div class="seats" id="lowerDeck">
                        @foreach ($lowerDeckSeats as $seat)
                            <div class="seat {{ $seat->TrangThai !== 'trống' ? 'taken' : '' }}"
                                 data-seat-id="{{ $seat->MaXeGhe }}" {{-- ID ghế (MaXeGhe) --}}
                                 data-seat-name="{{ $seat->SoGhe }}" {{-- Tên ghế (A1, B2) --}}
                                 onclick="{{ $seat->TrangThai === 'trống' ? "selectSeat(this)" : "" }}">
                                {{ $seat->SoGhe }}
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- GHẾ TẦNG TRÊN --}}
                 @if($upperDeckSeats->isNotEmpty())
                <div class="seating-area">
                    <h4>Ghế tầng trên</h4>
                    <div class="seats" id="upperDeck">
                         @foreach ($upperDeckSeats as $seat)
                            <div class="seat {{ $seat->TrangThai !== 'trống' ? 'taken' : '' }}"
                                 data-seat-id="{{ $seat->MaXeGhe }}"
                                 data-seat-name="{{ $seat->SoGhe }}"
                                 onclick="{{ $seat->TrangThai === 'trống' ? "selectSeat(this)" : "" }}">
                                {{ $seat->SoGhe }}
                            </div>
                        @endforeach
                    </div>
                </div>
                 @endif

                {{-- CHÚ THÍCH --}}
                <div class="legend">
                    <div class="legend-item"><div class="legend-box empty"></div><span>Ghế trống</span></div>
                    <div class="legend-item"><div class="legend-box selected"></div><span>Ghế đang chọn</span></div>
                    <div class="legend-item"><div class="legend-box taken"></div><span>Ghế đã bán/Hết</span></div>
                </div>
                 <div id="seatError" class="error-message" style="display: none;">Vui lòng chọn ít nhất một ghế.</div>
            </div>

            {{-- Cột Thông Tin & Tiếp Tục --}}
            <div class="col-md-5 col-lg-4">
                <div class="summary-card sticky-top-md"> {{-- Dính lại khi cuộn trên màn hình lớn --}}
                    <h2>THÔNG TIN CHUYẾN ĐI</h2>
                    <div class="info-row">
                        <span class="info-label">Tuyến:</span>
                        <span class="info-content">{{ $trip->TenLoTrinh }}</span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">Khởi hành:</span>
                        <span class="info-content">{{ \Carbon\Carbon::parse($trip->ThoiGianKhoiHanh)->format('H:i - d/m/Y') }}</span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">Loại xe:</span>
                        <span class="info-content">{{ $trip->TenLoaiXe }}</span>
                    </div>
                    <hr>
                    <h2>CHI TIẾT ĐẶT CHỖ</h2>
                     <div class="info-row">
                        <span class="info-label">Ghế đã chọn:</span>
                        <span class="info-content" id="selectedSeatsList">Chưa chọn ghế nào</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Số vé:</span>
                        <span class="info-content" id="selectedSeatsCount">0</span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">Giá vé:</span>
                        <span class="info-content">{{ number_format($trip->GiaNgayThuong, 0, ',', '.') }} VNĐ/vé</span>
                    </div>
                     <div class="info-row">
                        <span class="info-label">Tổng tiền:</span>
                        <strong class="info-content" id="totalPrice">0 VNĐ</strong>
                    </div>
                    <hr>
                    {{-- Form để chuyển sang bước tiếp theo --}}
                    <form action="{{ route('dat-ve.luu-ghe') }}" method="POST" id="seatSelectionForm" onsubmit="return validateSeatSelection()">
                        @csrf
                        <input type="hidden" name="maChuyenDi" value="{{ $maChuyenDi }}">
                        <input type="hidden" name="selectedSeatsInput" id="selectedSeatsInput" value=""> {{-- Chứa chuỗi ID ghế --}}
                        <input type="hidden" name="giaVe" id="giaVeInput" value="{{ $trip->GiaNgayThuong }}">
                        <input type="hidden" name="totalAmountInput" id="totalAmountInput" value="0">

                        <button type="submit" class="btn btn-success w-100">Tiếp tục</button>
                        <button type="button" class="btn btn-secondary w-100 mt-2" onclick="cancelSelection()">Hủy chọn</button>
                        {{-- Nút quay lại tìm kiếm nên giữ lại query cũ nếu có --}}
                        <a href="{{ route('search_ticket', session('search_params', [])) }}" class="btn btn-outline-secondary w-100 mt-2">Quay lại tìm kiếm</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedSeats = []; // Mảng lưu các object { id: MaXeGhe, name: SoGhe }
        const giaVe = parseFloat(document.getElementById('giaVeInput').value) || 0;
        function selectSeat(seatElement) {
            const seatId = seatElement.getAttribute('data-seat-id');
            const seatName = seatElement.getAttribute('data-seat-name');
            if (seatElement.classList.contains('taken')) return; // Không làm gì nếu ghế đã bán
            seatElement.classList.toggle('selected');
            const existingSeatIndex = selectedSeats.findIndex(seat => seat.id === seatId);
            if (seatElement.classList.contains('selected')) {
                if (existingSeatIndex === -1) { // Chỉ thêm nếu chưa có
                     selectedSeats.push({ id: seatId, name: seatName });
                }
            } else {
                if (existingSeatIndex > -1) { // Chỉ xóa nếu có
                    selectedSeats.splice(existingSeatIndex, 1);
                }
            }
            // Sắp xếp lại mảng theo tên ghế (A1, A10, B2) sau mỗi lần thay đổi
            selectedSeats.sort((a, b) => {
                const prefixA = a.name.substring(0, 1);
                const prefixB = b.name.substring(0, 1);
                const numA = parseInt(a.name.substring(1), 10);
                const numB = parseInt(b.name.substring(1), 10);
                if (prefixA < prefixB) return -1;
                if (prefixA > prefixB) return 1;
                return numA - numB;
            });
            document.getElementById('seatError').style.display = 'none'; // Ẩn lỗi khi có tương tác
            updateUI();
        }
        function updateUI() {
            const seatCount = selectedSeats.length;
            const seatNames = selectedSeats.map(seat => seat.name).join(', '); // Chuỗi tên ghế A1, A2...
            const seatIds = selectedSeats.map(seat => seat.id).join(',');       // Chuỗi ID ghế 1,5,15...
            const total = seatCount * giaVe;
            document.getElementById('selectedSeatsList').innerText = seatCount > 0 ? seatNames : 'Chưa chọn ghế nào';
            document.getElementById('selectedSeatsCount').innerText = seatCount;
            document.getElementById('totalPrice').innerText = total.toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('selectedSeatsInput').value = seatIds; // Cập nhật hidden input ID
            document.getElementById('totalAmountInput').value = total;     // Cập nhật hidden input tổng tiền
        }
        function cancelSelection() {
            selectedSeats.forEach(seat => {
                const seatElement = document.querySelector(`.seat[data-seat-id='${seat.id}']`);
                if (seatElement) {
                    seatElement.classList.remove('selected');
                }
            });
            selectedSeats = []; // Reset mảng
            updateUI();         // Cập nhật lại giao diện
        }
        function validateSeatSelection() {
            const seatIds = document.getElementById('selectedSeatsInput').value;
            if (!seatIds || seatIds.trim() === '') {
                 document.getElementById('seatError').style.display = 'block'; // Hiển thị lỗi
                return false; // Ngăn form submit
            }
             document.getElementById('seatError').style.display = 'none'; // Ẩn lỗi nếu đã chọn
            return true; // Cho phép submit
        }
         // Khởi tạo UI lần đầu
        document.addEventListener('DOMContentLoaded', updateUI);
    </script>

</x-book-ticket-layout>