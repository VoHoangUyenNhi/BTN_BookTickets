<x-book-ticket-layout>
    <x-slot name="title">Bao Toan Car</x-slot>

    <form name="frmTimKiem" id="frmTimKiem" method="GET" action="{{ route('search_ticket') }}">    

        <div class="search-container">
            <div class="search-bar">
                <!-- Điểm đi -->
                <div class="form-group">
                    <label for="diemdi">Điểm đi:</label>
                    <select class="form-control" id="diemdi" name="DiemDi">
                        <option value="">-- Chọn điểm đi --</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location }}" {{ request('DiemDi') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Điểm đến -->
                <div class="form-group">
                    <label for="diemden">Điểm đến:</label>
                    <select class="form-control" id="diemden" name="DiemDen">
                        <option value="">-- Chọn điểm đến --</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location }}" {{ request('DiemDen') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Ngày đi -->
                <div class="form-group">
                    <label for="ngaydi">Ngày đi</label>
                    <input id="ngaydi" name="ThoiGianKhoiHanh" type="date" class="form-control"
                        value="{{ old('ThoiGianKhoiHanh', request('ThoiGianKhoiHanh')) }}">
                </div> 

                <!-- Ngày về -->
                <div class="form-group" id="oNgayVe" style="display: none;">
                    <label for="ngayve">Ngày về</label>
                    <input id="ngayve" name="ngayve" type="date" class="form-control"
                        value="{{ old('ngayve', request('ngayve')) }}">
                </div>         
                <!-- Nút tìm -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Tìm chuyến</button>
                </div>
            </div>
        </div>
    </form>
</x-book-ticket-layout>
