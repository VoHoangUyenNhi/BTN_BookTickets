<x-account-panel>
    <div class="card w-75 mx-auto mt-5 shadow-sm border-0" style="background-color: #fffdfd;">
        <div class="card-body text-center px-5 py-4">
            <h4 class="text-primary mb-4" style="font-weight: 600;">THÔNG TIN CÁ NHÂN</h4>

            {{-- Ảnh đại diện --}}
            <div class="mb-3">
                @if ($user->photo)
                    <img src="{{ asset('storage/profile/' . $user->photo) }}" class="rounded-circle shadow-sm" width="120" height="120" style="object-fit: cover;" />
                @else
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 120px;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif
            </div>

            {{-- Thông tin người dùng --}}
            <div class="mx-auto text-left" style="max-width: 400px;">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><strong>Tên:</strong></span>
                    <span>{{ $user->name }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><strong>Email:</strong></span>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><strong>Số điện thoại:</strong></span>
                    <span>{{ $user->phone ?? 'Chưa có' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><strong>Ngày sinh:</strong></span>
                    <span>{{ $user->date ?? 'Chưa có' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><strong>Giới tính:</strong></span>
                    <span>{{ $user->gender ?? 'Chưa có' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><strong>Nghề nghiệp:</strong></span>
                    <span>{{ $user->career ?? 'Chưa có' }}</span>
                </div>
            </div>

            {{-- Nút cập nhật --}}
            <div class="mt-4">
                <a href="{{ route('account') }}" class="btn btn-outline-primary px-4">
                    <i class="fa fa-edit mr-1"></i> Cập nhật thông tin
                </a>
            </div>
        </div>
    </div>
</x-account-panel>
