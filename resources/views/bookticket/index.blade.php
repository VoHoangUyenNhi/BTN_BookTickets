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

                <!-- Loại vé -->
                <div class="form-group">
                    <label for="loaive">Loại vé</label>
                    <select id="loaive" name="loaive" class="form-control">
                        <option value="motchieu" {{ request('loaive') == 'motchieu' ? 'selected' : '' }}>Một chiều</option>
                        <option value="khuhoi" {{ request('loaive') == 'khuhoi' ? 'selected' : '' }}>Khứ hồi</option>
                    </select>
                </div>           

                <!-- Sắp xếp -->
                <div class="form-group">
                    <label for="sapxep">Sắp xếp theo:</label>
                    <select id="sapxep" name="sapxep" class="form-control">
                        <option value="gia_asc" {{ request('sapxep') == 'gia_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="gia_desc" {{ request('sapxep') == 'gia_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="thoi_gian_asc" {{ request('sapxep') == 'thoi_gian_asc' ? 'selected' : '' }}>Giờ đi sớm nhất</option>
                        <option value="thoi_gian_desc" {{ request('sapxep') == 'thoi_gian_desc' ? 'selected' : '' }}>Giờ đi muộn nhất</option>
                    </select>
                </div>  

                <!-- Nút tìm -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Tìm chuyến</button>
                </div>
            </div>
        </div>
    </form>
</x-book-ticket-layout>
