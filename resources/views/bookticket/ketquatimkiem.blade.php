<x-book-ticket-layout>
    <x-slot name="title">Kết quả tìm kiếm</x-slot>

    @if($trips->isEmpty())
        <p>Không tìm thấy chuyến xe phù hợp.</p>
    @else
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Mã chuyến đi</th>
            <th>Điểm đi</th>
            <th>Điểm đến</th>
            <th>Tên lộ trình</th>
            <th>Thời gian khởi hành</th>
            <th>Tên loại xe</th>
            <th>Giá ngày thường</th>
            <th>Số ghế trống</th>
            <th>Chọn chuyến</th> 
        </tr>
    </thead>
    <tbody>
        @foreach($trips as $trip)
        <tr>
            <td>{{ $trip->MaChuyenDi }}</td>
            <td>{{ $trip->DiemDi }}</td>
            <td>{{ $trip->DiemDen }}</td>
            <td>{{ $trip->TenLoTrinh }}</td>
            <td>{{ $trip->ThoiGianKhoiHanh }}</td>
            <td>{{ $trip->TenLoaiXe }}</td>
            <td>{{ number_format($trip->GiaNgayThuong, 0, ',', '.') }} VND</td> 
            <td>{{ $trip->SoGheTrong }}</td>
            <td>
            <a href="{{ route('bookticket.seat_selection', ['maChuyenDi' => $trip->MaChuyenDi]) }}" class="btn btn-primary btn-sm">Chọn ghế</a>               </td>
        </tr>
        @endforeach
    </tbody>
</table>
    @endif
</x-book-ticket-layout>