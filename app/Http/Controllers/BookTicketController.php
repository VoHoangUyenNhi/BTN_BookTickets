<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Thêm thư viện Carbon để xử lý ngày giờ
use Illuminate\Support\Facades\Validator; // <<< THÊM DÒNG NÀY
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log; // Thêm Log facade để ghi lỗi nếu cần


class BookTicketController extends Controller
{
    public function index()
    {
        // Lấy danh sách tên địa điểm từ bảng diem_di_qua
        $locations = DB::table('diemdiqua')->distinct()->pluck('TenDiaDiem');

        return view('bookticket.index', compact('locations'));
    }
    
    public function searchTrips(Request $request)
    {
        $diemDi = $request->input('DiemDi');
        $diemDen = $request->input('DiemDen');
        $ngayDi = $request->input('ThoiGianKhoiHanh');

        $trips = DB::table('chuyendi as cd')
        ->join('lotrinh AS lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
        ->join('diemdiqua AS dd_di', function ($join) use ($diemDi) {
            $join->on('lt.MaLoTrinh', '=', 'dd_di.MaLoTrinh')
                    ->where('dd_di.TenDiaDiem', '=', $diemDi);
        })
        ->join('diemdiqua AS dd_den', function ($join) use ($diemDen) {
            $join->on('lt.MaLoTrinh', '=', 'dd_den.MaLoTrinh')
                ->where('dd_den.TenDiaDiem', '=', $diemDen)
                ->where('dd_den.MaDiaDiem', '!=', 'dd_di.MaDiaDiem'); // Đảm bảo điểm đi và điểm đến khác nhau
        })
            ->join('banggia AS bg', 'cd.MaGia', '=', 'bg.MaGia')
            ->join('loaixe AS lx', 'bg.MaLoaiXe', '=', 'lx.MaLoaiXe')
            ->leftJoin(DB::raw('(SELECT COUNT(xg.MaXeGhe) AS SoGheTrong, x.MaLoaiXe FROM xeghe xg
                                JOIN xe x ON xg.MaXe = x.MaXe
                                JOIN maughetieuchuan mgtc ON mgtc.MaLoaiXe = x.MaLoaiXe AND mgtc.MaMauGheTieuChuan = xg.MaMauGheTieuChuan
                                WHERE xg.TrangThai = \'trống\' GROUP BY x.MaLoaiXe ) AS sg'), 'sg.MaLoaiXe', '=', 'lx.MaLoaiXe')
            ->select(
                'cd.MaChuyenDi',
                'dd_di.TenDiaDiem AS DiemDi',
                'dd_den.TenDiaDiem AS DiemDen',
                'lt.TenLoTrinh',
                'cd.ThoiGianKhoiHanh',
                'lx.TenLoaiXe',
                'bg.GiaNgayThuong',
                'sg.SoGheTrong',
                'cd.MaChuyenDi'
            )
            ->whereDate('cd.ThoiGianKhoiHanh', $ngayDi)
            ->where('cd.loaive', 'Một chiều')
            ->get();

            return view('bookticket.ketquatimkiem', compact('trips'));
        }
        public function showSeatSelection($maChuyenDi)
    {
        // 1. Lấy thông tin cơ bản của chuyến đi và xe chạy chuyến đó
        $tripInfo = DB::table('chuyendi as cd')
            ->join('lotrinh as lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
            ->join('xe', 'cd.MaXe', '=', 'xe.MaXe') // Quan trọng: Join với xe cụ thể
            ->join('loaixe as lx', 'xe.MaLoaiXe', '=', 'lx.MaLoaiXe')
            ->join('banggia as bg', 'cd.MaGia', '=', 'bg.MaGia')
            ->select(
                'cd.MaChuyenDi', 'cd.ThoiGianKhoiHanh', 'cd.MaXe',
                'lt.TenLoTrinh', 'lt.ThoiGianDiChuyen',
                'lx.TenLoaiXe', 'lx.SoGhe', // Tổng số ghế của xe
                'bg.GiaNgayThuong', 'bg.GiaNgayDacBiet',
                'xe.BienSoXe' // Lấy biển số xe nếu cần hiển thị
            )
            ->where('cd.MaChuyenDi', $maChuyenDi)
            ->whereNotNull('cd.MaXe') // Đảm bảo chuyến đi đã được gán xe
            ->first(); // Lấy một dòng kết quả

        if (!$tripInfo) {
            abort(404, 'Không tìm thấy thông tin chuyến đi hoặc chuyến đi chưa được xếp xe.');
        }

        // 2. Xác định giá vé áp dụng cho chuyến đi này
        $ngayKhoiHanh = Carbon::parse($tripInfo->ThoiGianKhoiHanh);
        // --> THAY THẾ BẰNG LOGIC XÁC ĐỊNH NGÀY ĐẶC BIỆT CỦA BẠN <--
        $isSpecialDay = $ngayKhoiHanh->isWeekend(); // Ví dụ
        $giaApDung = $isSpecialDay ? $tripInfo->GiaNgayDacBiet : $tripInfo->GiaNgayThuong;

        // 3. Lấy danh sách tất cả ghế thuộc xe này (từ xeghe và maughetieuchuan)
        $allSeats = DB::table('xeghe as xg')
            ->join('maughetieuchuan as mgtc', 'xg.MaMauGheTieuChuan', '=', 'mgtc.MaMauGheTieuChuan')
            ->select('xg.MaXeGhe', 'mgtc.MaGhe', 'mgtc.LoaiGhe')
            ->where('xg.MaXe', $tripInfo->MaXe) // Lọc theo xe cụ thể của chuyến đi
            ->orderBy('mgtc.LoaiGhe') // Sắp xếp theo tầng
            ->orderBy('mgtc.MaGhe')   // Sắp xếp theo mã ghế
            ->get();

        // 4. Lấy danh sách MaXeGhe đã được đặt cho chuyến đi NÀY
        $bookedSeatIds = DB::table('phieudatxe as pd')
            ->join('chitietphieudatxe as ctpd', 'pd.MaPhieuDat', '=', 'ctpd.MaPhieuDat')
            ->where('pd.MaChuyenDi', $maChuyenDi)
            //->get()
            ->pluck('MaGhe')
            ->toArray();

        // 5. Gộp thông tin ghế và trạng thái đặt chỗ
        $seatsWithStatus = $allSeats->map(function ($seat) use ($bookedSeatIds) {
            $isBooked = in_array($seat->MaXeGhe, $bookedSeatIds);
            $seat->TrangThai = $isBooked ? 'da_ban' : 'trong';
            return $seat;
        });

        // Phân loại ghế theo tầng
        $lowerDeckSeats = $seatsWithStatus->where('LoaiGhe', 'Tầng dưới');
        $upperDeckSeats = $seatsWithStatus->where('LoaiGhe', 'Tầng trên');

        // 6. Lấy danh sách điểm đón và điểm trả cho lộ trình của chuyến đi
        $pickupPoints = DB::table('diemdontra')
            ->where('MaLoTrinh', function ($query) use ($maChuyenDi) {
                $query->select('MaLoTrinh')->from('chuyendi')->where('MaChuyenDi', $maChuyenDi);
            })
            ->where('LoaiDiem', 'don')
            ->select('MaDiemDonTra', 'TenDiem')
            ->get();

        $dropoffPoints = DB::table('diemdontra')
            ->where('MaLoTrinh', function ($query) use ($maChuyenDi) {
                $query->select('MaLoTrinh')->from('chuyendi')->where('MaChuyenDi', $maChuyenDi);
            })
            ->where('LoaiDiem', 'tra')
            ->select('MaDiemDonTra', 'TenDiem')
            ->get();

        // 7. Truyền tất cả dữ liệu sang view
        return view('bookticket.chon_ghe', compact(
            'tripInfo',
            'giaApDung',
            'lowerDeckSeats',
            'upperDeckSeats',
            'pickupPoints',
            'dropoffPoints'
        ));

    }
    public function processSeatSelection(Request $request)
    {
        // --- 1. Validate dữ liệu từ form ---
        $rules = [ // Tách rules ra một biến riêng cho dễ đọc
            'maChuyenDi' => 'required|integer|exists:chuyendi,MaChuyenDi',
            'selectedSeats' => 'required|string',
            'totalAmount' => 'required|numeric|min:0',
            'giaVe' => 'required|numeric|min:0',
            // Xóa required_if ở đây, chỉ giữ lại các rules khác cho name
            'name' => 'nullable|string|max:255',
'phone' => [ // Sử dụng cú pháp mảng
                'required',
                'string',
                // Thêm lại dấu / ở đầu và cuối regex
                'regex:/^((0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7})$/'
            ],            'email' => 'required|email|max:255',
            'pickup' => 'required|integer|exists:diemdontra,MaDiemDonTra',
            'dropoff' => 'required|integer|exists:diemdontra,MaDiemDonTra',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'accepted',
        ];

        $messages = [ // Tách messages ra
            'selectedSeats.required' => 'Vui lòng chọn ít nhất một ghế.',
            'name.required' => 'Vui lòng nhập họ và tên.', // Sửa message thành required
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'pickup.required' => 'Vui lòng chọn điểm đón.',
            'pickup.exists' => 'Điểm đón không hợp lệ.',
            'dropoff.required' => 'Vui lòng chọn điểm trả.',
            'dropoff.exists' => 'Điểm trả không hợp lệ.',
            'terms.accepted' => 'Bạn phải đồng ý với điều khoản và chính sách.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // === THÊM ĐOẠN CODE NÀY ===
        // Áp dụng rule 'required' cho 'name' CHỈ KHI người dùng là khách (guest)
        $validator->sometimes('name', 'required', function ($input) {
            return auth()->guest(); // Điều kiện: là khách
        });

        if ($validator->fails()) 
        {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput(); // Giữ lại dữ liệu đã nhập trên form
        }

        // Lấy dữ liệu đã validate
        $validated = $validator->validated();

        // --- 2. Lấy thêm thông tin cần thiết ---
        try {
            // Lấy thông tin chuyến đi cơ bản
            $tripInfo = DB::table('chuyendi as cd')
                ->join('lotrinh as lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
                ->select('lt.TenLoTrinh', 'cd.ThoiGianKhoiHanh')
                ->where('cd.MaChuyenDi', $validated['maChuyenDi'])
                ->first();

            if(!$tripInfo) {
                 throw new \Exception('Không tìm thấy thông tin chuyến đi.');
            }

            // Lấy tên điểm đón/trả
            $pickupPoint = DB::table('diemdontra')->where('MaDiemDonTra', $validated['pickup'])->value('TenDiem');
            $dropoffPoint = DB::table('diemdontra')->where('MaDiemDonTra', $validated['dropoff'])->value('TenDiem');

            if(!$pickupPoint || !$dropoffPoint) {
                 throw new \Exception('Không tìm thấy thông tin điểm đón hoặc điểm trả.');
            }

            // Chuyển chuỗi selectedSeats (MaXeGhe) thành mảng ID
            $selectedSeatIds = explode(',', $validated['selectedSeats']);
            $selectedSeatIds = array_map('intval', $selectedSeatIds); // Chuyển sang kiểu integer
            $selectedSeatIds = array_filter($selectedSeatIds); // Loại bỏ giá trị rỗng nếu có

            if (empty($selectedSeatIds)) {
                 throw new \Exception('Danh sách ghế chọn không hợp lệ.');
            }

            // Lấy mã ghế (A1, B2..) tương ứng với MaXeGhe đã chọn
             $selectedSeatCodes = DB::table('xeghe as xg')
                ->join('maughetieuchuan as mgtc', 'xg.MaMauGheTieuChuan', '=', 'mgtc.MaMauGheTieuChuan')
                ->whereIn('xg.MaXeGhe', $selectedSeatIds)
                ->pluck('mgtc.MaGhe')
                ->toArray(); // Lấy mảng các mã ghế

            // Sắp xếp lại mã ghế theo thứ tự tự nhiên để hiển thị
            natsort($selectedSeatCodes);
            $selectedSeatCodesString = implode(', ', $selectedSeatCodes);

            // --- 3. Chuẩn bị dữ liệu để lưu vào session ---
            $bookingData = [
                'maChuyenDi' => $validated['maChuyenDi'],
                'tenLoTrinh' => $tripInfo->TenLoTrinh,
                'thoiGianKhoiHanh' => $tripInfo->ThoiGianKhoiHanh,
                'maXeGheIds' => $selectedSeatIds, // Mảng ID để lưu vào chitietphieudatxe
                'seatCodesDisplay' => $selectedSeatCodesString, // Chuỗi tên ghế để hiển thị
                'tongTien' => $validated['totalAmount'],
                // Lấy thông tin từ user đã đăng nhập hoặc từ form
                'hoTen' => auth()->check() ? auth()->user()->name : $validated['name'],
                'soDienThoai' => $validated['phone'], // Luôn lấy SĐT mới nhất từ form
                'email' => $validated['email'],      // Luôn lấy Email mới nhất từ form
                'maKhachHang' => auth()->check() ? auth()->id() : null, // ID user nếu đăng nhập
                'maDiemDon' => $validated['pickup'],
                'tenDiemDon' => $pickupPoint,
                'maDiemTra' => $validated['dropoff'],
                'tenDiemTra' => $dropoffPoint,
                'ghiChu' => $validated['notes'] ?? null,
            ];

            // --- 4. Lưu dữ liệu vào Session ---
            $request->session()->put('booking_confirmation_data', $bookingData);

            // --- 5. Chuyển hướng đến trang xác nhận ---
            return redirect()->route('bookticket.show_confirmation');

        } catch (\Exception $e) {
            // Xử lý lỗi nếu có (ví dụ: không tìm thấy thông tin)
            return redirect()->back()
                        ->with('error', 'Đã xảy ra lỗi khi xử lý yêu cầu: ' . $e->getMessage())
                        ->withInput();
        }
    }

    // *** THÊM PHƯƠNG THỨC NÀY ***
    public function showConfirmationPage(Request $request)
    {
        // Lấy dữ liệu từ session
        $bookingData = $request->session()->get('booking_confirmation_data');

        // Kiểm tra xem có dữ liệu không (tránh truy cập trực tiếp)
        if (!$bookingData) {
            return redirect()->route('home')->with('error', 'Không có thông tin đặt vé để xác nhận.');
        }

        // Hiển thị view xác nhận và truyền dữ liệu sang
        return view('bookticket.xac_nhan_dat_ve', compact('bookingData'));
    }
    public function finalizeBooking(Request $request)
    {
        // 1. Lấy dữ liệu từ session
        $bookingData = $request->session()->get('booking_confirmation_data');

        // 2. Kiểm tra xem có dữ liệu không
        if (!$bookingData) {
            // Nếu không có dữ liệu (ví dụ: user refresh trang success, session hết hạn)
            return redirect()->route('home')->with('error', 'Phiên đặt vé đã hết hạn hoặc không hợp lệ.');
        }

        // 3. Thực hiện lưu vào Database trong một Transaction
        DB::beginTransaction(); // Bắt đầu Transaction

        try {
            // --- 3.1. Lưu vào bảng phieudatxe ---
            $maPhieuDat = DB::table('phieudatxe')->insertGetId([
                'MaKhachHang' => $bookingData['maKhachHang'], // null nếu là khách vãng lai
                'ThoiGianDat' => now(), // Thời gian hiện tại
                'MaChuyenDi' => $bookingData['maChuyenDi'],
                // Bỏ cột 'SoGhe', sẽ tính từ chi tiết
                'TongSoTien' => $bookingData['tongTien'],
                'HinhThucThanhToan' => 'Tại nhà xe', // Hoặc giá trị bạn muốn lưu
                'TenKhachHang' => $bookingData['hoTen'],
                'SoDienThoai' => $bookingData['soDienThoai'],
                'Email' => $bookingData['email'],
                'MaDiemDon' => $bookingData['maDiemDon'],
                // 'GhiChuDiemDon' => null, // Cột này hiện không có trong $bookingData
                'MaDiemTra' => $bookingData['maDiemTra'],
                // 'GhiChuDiemTra' => null, // Cột này hiện không có trong $bookingData
                'GhiChu' => $bookingData['ghiChu'], // Thêm cột GhiChu nếu chưa có
                'TrangThaiThanhToan' => 'ChuaThanhToan', // Trạng thái mặc định
                // Bỏ cột 'DanhSachGhe', 'DanhSachTenGhe'
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // --- 3.2. Chuẩn bị dữ liệu cho bảng chitietphieudatxe ---
            $chiTietPhieuData = [];
            // Đổi tên biến này cho rõ ràng hơn (nhưng không bắt buộc)
            // $selectedMaXeGheIds = $bookingData['maXeGheIds'];
            $maXeGheIds = $bookingData['maXeGheIds']; // Lấy mảng các MaXeGhe từ session

            // Tên cột trong bảng chitietphieudatxe là 'MaGhe'
            $seatIdColumn = 'MaGhe';

            // Biến trong vòng lặp là $maXeGheId (chứa giá trị ID ghế của lần lặp hiện tại)
            foreach ($maXeGheIds as $maXeGheId) {
                $chiTietPhieuData[] = [
                    'MaPhieuDat' => $maPhieuDat,
                    // Key là 'MaGhe', Value phải là biến chứa ID ghế hiện tại ($maXeGheId)
                    $seatIdColumn => $maXeGheId, // Sử dụng tên cột đã xác định
                    // Nếu bảng chitietphieudatxe có timestamp thì thêm vào
                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                ];
            }

            // --- 3.3. Lưu vào bảng chitietphieudatxe ---
            if (!empty($chiTietPhieuData)) {
                // >>> Sửa tên bảng nếu cần <<<
                DB::table('chitietphieudatxe')->insert($chiTietPhieuData);
            } else {
                // Nếu không có chi tiết ghế nào thì rollback và báo lỗi
                throw new \Exception('Không có thông tin chi tiết ghế để lưu.');
            }

            // --- 3.4. Commit Transaction nếu mọi thứ thành công ---
            DB::commit();

            // 4. Xóa dữ liệu khỏi session sau khi lưu thành công
            $request->session()->forget('booking_confirmation_data');

            // 5. Chuyển hướng đến trang thành công với thông báo và mã phiếu đặt
            return redirect()->route('bookticket.success')
                         ->with('successMessage', 'Đặt vé thành công!')
                         ->with('maPhieuDat', $maPhieuDat); // Gửi mã phiếu đặt sang trang thành công

        } catch (\Exception $e) {
            // --- 3.5. Rollback Transaction nếu có lỗi xảy ra ---
            DB::rollBack();

            // Ghi lại lỗi để debug
            Log::error('Lỗi khi đặt vé: ' . $e->getMessage() . ' - Data: ' . json_encode($bookingData));

            // Quay lại trang xác nhận với thông báo lỗi
            return redirect()->route('bookticket.show_confirmation')
                         ->with('error', 'Đã xảy ra lỗi trong quá trình đặt vé. Vui lòng thử lại. Lỗi: ' . $e->getMessage());
            // Hoặc quay về trang chọn ghế/trang chủ nếu trang xác nhận không còn hợp lệ
            // return redirect()->back()->with('error', ...)->withInput();
        }
    }
    // *** KẾT THÚC PHƯƠNG THỨC finalizeBooking ***

    // *** THÊM PHƯƠNG THỨC HIỂN THỊ TRANG THÀNH CÔNG ***
     public function showSuccessPage(Request $request)
    {
        // Lấy thông báo và mã phiếu đặt từ flash session
        $successMessage = $request->session()->get('successMessage');
        $maPhieuDat = $request->session()->get('maPhieuDat');

        // Nếu không có thông báo (ví dụ: truy cập trực tiếp URL) thì về trang chủ
        if (!$successMessage) {
             return redirect()->route('home');
        }

        return view('bookticket.dat_ve_thanh_cong', compact('successMessage', 'maPhieuDat'));
    }
}
