<x-account-panel>
    <form method="POST" action="{{ route('saveinfo') }}" enctype="multipart/form-data" class="w-75 mx-auto mt-4 p-4 border rounded shadow-sm bg-light">
        @csrf
        <div class="text-center mb-3 text-primary fw-bold fs-5">CẬP NHẬT THÔNG TIN CÁ NHÂN</div>

        <div class="row">
            {{-- Cột trái: Thông tin --}}
            <div class="col-md-8">
                <div class="form-group mb-3">
                    <label for="name">Tên</label>
                    <input type="text" class="form-control form-control-sm" name="name" value="{{ $user->name }}">
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="text" class="form-control form-control-sm" name="email" value="{{ $user->email }}">
                </div>

                <div class="form-group mb-3">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control form-control-sm" name="phone" value="{{ $user->phone }}">
                </div>

                <div class="form-group mb-3">
                    <label for="date">Ngày sinh</label>
                    <input type="date" class="form-control form-control-sm" name="date" value="{{ $user->date }}">
                </div>

                <div class="form-group mb-3">
                    <label for="gender">Giới tính</label>
                    <select name="gender" class="form-control form-control-sm">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="Nam" {{ $user->gender == 'Nam' ? 'selected' : '' }}>Nam</option>
                        <option value="Nữ" {{ $user->gender == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        <option value="Khác" {{ $user->gender == 'Khác' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="career">Nghề nghiệp</label>
                    <input type="text" class="form-control form-control-sm" name="career" value="{{ $user->career }}">
                </div>

                <input type="hidden" name="id" value="{{ $user->id }}">

                <div class="text-center mt-3">
                    <a href="{{ route('profileinfo') }}" class="btn btn-primary">Quay về</a>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </div>

            {{-- Cột phải: Ảnh đại diện --}}
            <div class="col-md-4 text-center">
                <label for="photo" class="form-label">Ảnh đại diện</label><br>
                @if ($user->photo)
                    <img src="{{ asset('storage/profile/' . $user->photo) }}" class="img-thumbnail mb-2" width="120px" />
                @else
                    <div class="mb-2" style="width: 120px; height: 120px; border-radius: 50%; background: #e9ecef; line-height: 120px;">
                        No image
                    </div>
                @endif
                <input type="file" class="form-control mt-2" name="photo" accept="image/*" style='height: 40px;'>
            </div>
        </div>
    </form>
    @if ($errors->any())
        <div class="alert alert-danger w-75 mx-auto">
            <strong>Whoops!</strong> Đã xảy ra lỗi:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success w-75 mx-auto">
            {{ session('status') }}
        </div>
    @endif
</x-account-panel>
