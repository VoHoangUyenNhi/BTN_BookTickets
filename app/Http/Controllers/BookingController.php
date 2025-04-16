<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Mail; // Bỏ comment nếu dùng gửi mail
// use App\Mail\BookingConfirmationMail; // Tạo Mail class nếu cần

class BookingController extends Controller
{
    /**
     * Bước 1: Hiển thị trang chọn ghế cho một chuyến đi cụ thể.
     * Route: dat-ve.chon-ghe (GET)
     */
    public function showSeatSelection($maChuyenDi)
    {
        // 1. Lấy thông tin chuyến đi, giá, loại xe, và MaXe (Quan trọng!)
        $trip = DB::table('chuyendi as cd')
            ->join('lotrinh AS lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
            ->join('banggia AS bg', 'cd.MaGia', '=', 'bg.MaGia')
            ->join('loaixe AS lx', 'bg.MaLoaiXe', '=', 'lx.MaLoaiXe')
            ->select(
                'cd.MaChuyenDi', 'lt.TenLoTrinh', 'lt.ThoiGianDiChuyen',
                'cd.ThoiGianKhoiHanh', 'lx.TenLoaiXe', 'bg.GiaNgayThuong',
                'cd.MaXe' // *** Lấy MaXe từ bảng chuyendi ***
            )
            ->where('cd.MaChuyenDi', $maChuyenDi)
            // Chỉ cho chọn ghế chuyến chưa khởi hành
            ->where('cd.ThoiGianKhoiHanh', '>', now())
            ->first();

        // Kiểm tra xem chuyến đi và MaXe có tồn tại không
        if (!$trip || !$trip->MaXe) {
            abort(404, 'Không tìm thấy thông tin chuyến đi hoặc xe chưa được phân công.');
        }

        $maXeChayChuyen = $trip->MaXe;

        // 2. Lấy danh sách ghế của xe đó từ xeghe và maughetieuchuan
        $allSeats = DB::table('xeghe as xg')
            ->join('maughetieuchuan as tg', 'xg.MaMauGheTieuChuan', '=', 'tg.MaMauGheTieuChuan')
            ->select(
                 'tg.MaGhe as SoGhe',   // Tên ghế (vd: A1) từ maughetieuchuan
                 'tg.LoaiGhe',          // Tầng ghế từ maughetieuchuan
                 'xg.TrangThai',        // Trạng thái từ xeghe
                 'xg.MaXeGhe'           // ID duy nhất của ghế trên xe
            )
            ->where('xg.MaXe', $maXeChayChuyen) // Lọc theo xe cụ thể
            ->orderBy('tg.LoaiGhe') // Sắp xếp Tầng dưới trước
            ->orderByRaw('SUBSTRING(tg.MaGhe, 1, 1)') // Sắp xếp theo chữ cái đầu
            ->orderByRaw('CAST(SUBSTRING(tg.MaGhe, 2) AS UNSIGNED)') // Sắp xếp theo số
            ->get();

        if ($allSeats->isEmpty()){
            abort(500, 'Xe này chưa được cấu hình ghế ngồi.');
        }

        // Phân loại ghế theo tầng
        $lowerDeckSeats = $allSeats->where('LoaiGhe', 'Tầng dưới');
        $upperDeckSeats = $allSeats->where('LoaiGhe', 'Tầng trên');

        // Xóa session đặt vé cũ nếu có
        Session::forget(['booking_data']);

        // Trả về view chọn ghế với dữ liệu cần thiết
        return view('bookticket.chon-ghe', compact(
            'trip',
            'lowerDeckSeats',
            'upperDeckSeats',
            'maChuyenDi' // Truyền lại MaChuyenDi để dùng trong form
        ));
    }

    /**
     * Bước 2: Lưu thông tin ghế đã chọn vào session và chuyển hướng.
     * Route: dat-ve.luu-ghe (POST)
     */
    public function storeSelectedSeats(Request $request)
    {
        // Validate dữ liệu gửi lên từ form chọn ghế
        $validated = $request->validate([
            'maChuyenDi' => 'required|integer|exists:chuyendi,MaChuyenDi',
            'selectedSeatsInput' => 'required|string', // Chuỗi MaXeGhe, vd "1,5,15"
            'giaVe' => 'required|numeric|min:0',
            'totalAmountInput' => 'required|numeric|min:0',
        ]);

        $seatIds = explode(',', $validated['selectedSeatsInput']); // Chuyển chuỗi thành mảng ID
        $maChuyenDi = $validated['maChuyenDi'];

        // Lấy MaXe của chuyến đi để kiểm tra ghế
        $maXe = DB::table('chuyendi')->where('MaChuyenDi', $maChuyenDi)->value('MaXe');
        if (!$maXe) {
            // Nếu không tìm thấy MaXe (dữ liệu lỗi), quay lại trang chọn ghế
            return redirect()->route('dat-ve.chon-ghe', ['maChuyenDi' => $maChuyenDi])
                            ->withErrors(['error' => 'Lỗi: Không xác định được xe của chuyến đi.']);
        }

        // Kiểm tra lại xem các ghế này có thực sự tồn tại và trống trên ĐÚNG XE này không
        $availableSeatsCount = DB::table('xeghe')
            ->where('MaXe', $maXe) // Chỉ kiểm tra trên xe này
            ->whereIn('MaXeGhe', $seatIds) // Chỉ các ghế đã chọn
            ->where('TrangThai', 'trống') // Chỉ ghế còn trống
            ->count();

        // Nếu số ghế trống tìm được không khớp số ghế đã chọn -> có lỗi
        if ($availableSeatsCount !== count($seatIds)) {
            return redirect()->route('dat-ve.chon-ghe', ['maChuyenDi' => $maChuyenDi])
                            ->withErrors(['seat_error' => 'Một hoặc nhiều ghế bạn chọn không còn trống hoặc không hợp lệ. Vui lòng chọn lại.']);
        }

        // Lấy lại tên ghế (từ maughetieuchuan.MaGhe) để hiển thị ở các bước sau
        $seatNames = DB::table('xeghe as xg')
            ->join('maughetieuchuan as tg', 'xg.MaMauGheTieuChuan', '=', 'tg.MaMauGheTieuChuan')
            ->whereIn('xg.MaXeGhe', $seatIds)
            ->orderByRaw('SUBSTRING(tg.MaGhe, 1, 1)')
            ->orderByRaw('CAST(SUBSTRING(tg.MaGhe, 2) AS UNSIGNED)')
            ->pluck('tg.MaGhe') // Lấy cột MaGhe (tên ghế)
            ->implode(', '); // Nối thành chuỗi, vd: "A1, A5, B10"

        // Lưu các thông tin cần thiết vào session để dùng ở bước tiếp theo
        Session::put('booking_data', [
            'maChuyenDi' => $maChuyenDi,
            'selectedSeatIds' => $seatIds,        // Mảng ID ghế (MaXeGhe)
            'selectedSeatNames' => $seatNames,    // Chuỗi tên ghế (A1, B2)
            'giaVe' => $validated['giaVe'],       // Giá vé đơn vị
            'totalAmount' => $validated['totalAmountInput'], // Tổng tiền
            'seatCount' => count($seatIds),       // Số lượng ghế
            'maXe' => $maXe                        // Mã xe chạy chuyến này
        ]);

        // Chuyển hướng đến trang nhập thông tin khách hàng
        return redirect()->route('dat-ve.chi-tiet');
    }

     /**
      * Bước 3: Hiển thị form nhập thông tin khách hàng và điểm đón/trả.
      * Route: dat-ve.chi-tiet (GET)
      */
    public function showCustomerDetailsForm()
    {
        // Lấy dữ liệu đặt vé từ session
        $bookingData = Session::get('booking_data');

        // Nếu không có dữ liệu trong session (vd: hết hạn, lỗi), quay về trang chủ
        if (!$bookingData || empty($bookingData['selectedSeatIds'])) {
            return redirect()->route('home')->withErrors(['session_error' => 'Phiên đặt vé đã hết hạn hoặc có lỗi. Vui lòng tìm lại chuyến đi.']);
        }

        $maChuyenDi = $bookingData['maChuyenDi'];

        // Lấy lại thông tin cơ bản của chuyến đi và loại xe
        $trip = DB::table('chuyendi as cd')
            ->join('lotrinh AS lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
            // Join đến bảng xe dựa trên MaXe đã lưu trong session
            ->join('xe', 'xe.MaXe', '=', DB::raw($bookingData['maXe']))
            // Join đến bảng loaixe từ bảng xe
            ->join('loaixe AS lx', 'xe.MaLoaiXe', '=', 'lx.MaLoaiXe')
            ->select('cd.MaChuyenDi', 'lt.TenLoTrinh', 'cd.ThoiGianKhoiHanh', 'lx.TenLoaiXe', 'cd.MaLoTrinh')
            ->where('cd.MaChuyenDi', $maChuyenDi)
            ->first();

        // Nếu không tìm thấy chuyến đi (dù không nên xảy ra), quay về trang chủ
        if (!$trip) {
            Session::forget('booking_data'); // Xóa session lỗi
            return redirect()->route('home')->withErrors(['error' => 'Không tìm thấy thông tin chuyến đi liên quan.']);
        }

        // Lấy danh sách điểm đón/trả từ bảng diemdontra theo MaLoTrinh của chuyến đi
        $pickupPoints = DB::table('diemdontra')
            ->where('MaLoTrinh', $trip->MaLoTrinh)
            // ->where('LoaiDiem', 'don') // Bỏ comment nếu có cột LoaiDiem và muốn lọc
            ->orderBy('TenDiem')
            ->get(['MaDiemDonTra', 'TenDiem']); // Chỉ lấy mã và tên

        $dropoffPoints = DB::table('diemdontra')
            ->where('MaLoTrinh', $trip->MaLoTrinh)
            // ->where('LoaiDiem', 'tra') // Bỏ comment nếu có cột LoaiDiem và muốn lọc
            ->orderBy('TenDiem')
            ->get(['MaDiemDonTra', 'TenDiem']); // Chỉ lấy mã và tên

        // Trả về view nhập thông tin với các dữ liệu cần thiết
        return view('bookticket.chi-tiet-ve', compact(
            'bookingData', // Dữ liệu từ session (ghế, giá,...)
            'trip',        // Thông tin chuyến đi
            'pickupPoints',// Danh sách điểm đón
            'dropoffPoints'// Danh sách điểm trả
        ));
    }

    /**
     * Bước 4: Xử lý thông tin khách hàng, lưu vé vào DB, cập nhật ghế.
     * Route: dat-ve.xac-nhan (POST)
     */
    public function processBooking(Request $request)
    {
        // Lấy dữ liệu đặt vé từ session
        $bookingData = Session::get('booking_data');
        // Nếu không có session, quay về trang chủ
        if (!$bookingData || empty($bookingData['selectedSeatIds'])) {
            return redirect()->route('home')->withErrors(['error' => 'Phiên đặt vé không hợp lệ hoặc đã hết hạn.']);
        }

        // Validate dữ liệu khách hàng gửi lên từ form
        $validatedCustomer = $request->validate([
            'name' => 'required|string|max:255',
            // Regex chuẩn hơn cho SĐT Việt Nam
            'phone' => ['required', 'string', 'regex:/^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/', 'max:15'],
            'email' => 'required|email|max:255',
            'terms' => 'required|accepted', // Checkbox điều khoản phải được chọn
            'pickup_point' => 'required|integer|exists:diemdontra,MaDiemDonTra', // Điểm đón phải tồn tại
            'pickup_notes' => 'nullable|string|max:500', // Ghi chú điểm đón (không bắt buộc)
            'dropoff_point' => 'required|integer|exists:diemdontra,MaDiemDonTra', // Điểm trả phải tồn tại
            'dropoff_notes' => 'nullable|string|max:500', // Ghi chú điểm trả (không bắt buộc)
            // 'payment_method' => 'required|string', // Thêm nếu có chọn phương thức thanh toán
        ]);

        // Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            $maChuyenDi = $bookingData['maChuyenDi'];
            $selectedSeatIds = $bookingData['selectedSeatIds'];
            $maXe = $bookingData['maXe']; // Lấy MaXe từ session

            // *** KIỂM TRA GHẾ LẦN CUỐI CÙNG (RẤT QUAN TRỌNG) ***
            // Lấy trạng thái hiện tại của các ghế đã chọn trên đúng xe
            $lockedSeats = DB::table('xeghe')
                ->where('MaXe', $maXe)
                ->whereIn('MaXeGhe', $selectedSeatIds)
                // ->lockForUpdate() // Cân nhắc dùng để khóa hàng, tránh race condition tốt hơn
                ->get();

            // Tìm những ghế không còn trống trong danh sách đã chọn
            $unavailableSeats = $lockedSeats->where('TrangThai', '!=', 'trống');

            // Nếu có ghế không còn trống -> rollback và báo lỗi
            if ($unavailableSeats->isNotEmpty()) {
                DB::rollBack(); // Hủy bỏ transaction
                // Lấy tên các ghế bị trùng để thông báo
                $unavailableSeatNames = DB::table('xeghe as xg')
                   ->join('maughetieuchuan as tg', 'xg.MaMauGheTieuChuan', '=', 'tg.MaMauGheTieuChuan')
                   ->whereIn('xg.MaXeGhe', $unavailableSeats->pluck('MaXeGhe'))
                   ->pluck('tg.MaGhe') // Lấy tên ghế
                   ->implode(', ');

                 // Quay lại trang chọn ghế với thông báo lỗi cụ thể
                 return redirect()->route('dat-ve.chon-ghe', ['maChuyenDi' => $maChuyenDi])
                                  ->withErrors(['seat_error' => 'Rất tiếc, ghế ' . $unavailableSeatNames . ' vừa có người khác đặt. Vui lòng chọn lại.']);
            }

            // *** Lưu thông tin đặt vé vào bảng 'phieudatxe' ***
            // Đảm bảo bảng 'phieudatxe' đã có đủ các cột được liệt kê dưới đây
            $bookingId = DB::table('phieudatxe')->insertGetId([
                // MaPhieuDat là auto-increment
                'MaKhachHang' => Auth::id(), // ID người dùng nếu đăng nhập, null nếu không
                'ThoiGianDat' => Carbon::now(), // Thời gian hiện tại
                'MaChuyenDi' => $maChuyenDi,
                'SoGhe' => $bookingData['seatCount'],           // Số lượng ghế
                'TongSoTien' => $bookingData['totalAmount'],    // Tổng tiền
                'HinhThucThanhToan' => $request->input('payment_method', 'Chưa chọn'), // Lấy từ form hoặc mặc định

                // -- Các cột cần thêm vào bảng phieudatxe --
                'TenKhachHang' => $validatedCustomer['name'],
                'SoDienThoai' => $validatedCustomer['phone'],
                'Email' => $validatedCustomer['email'],
                'MaDiemDon' => $validatedCustomer['pickup_point'],
                'GhiChuDiemDon' => $validatedCustomer['pickup_notes'],
                'MaDiemTra' => $validatedCustomer['dropoff_point'],
                'GhiChuDiemTra' => $validatedCustomer['dropoff_notes'],
                'TrangThaiThanhToan' => 'ChuaThanhToan', // Mặc định khi mới đặt
                'DanhSachGhe' => implode(',', $selectedSeatIds), // Chuỗi MaXeGhe
                'DanhSachTenGhe' => $bookingData['selectedSeatNames'], // Chuỗi tên ghế
                'created_at' => Carbon::now(), // Nếu dùng timestamp của Laravel
                'updated_at' => Carbon::now(), // Nếu dùng timestamp của Laravel
                // -- Hết các cột cần thêm --
            ]);

            // *** Cập nhật trạng thái các ghế đã chọn trong 'xeghe' ***
            $updatedRows = DB::table('xeghe')
                ->where('MaXe', $maXe) // Chỉ cập nhật ghế của đúng xe này
                ->whereIn('MaXeGhe', $selectedSeatIds) // Chỉ các ghế đã chọn
                ->where('TrangThai', 'trống') // Chỉ cập nhật ghế đang trống (đảm bảo an toàn)
                ->update([
                    'TrangThai' => 'da_ban', // Đổi trạng thái thành đã bán (hoặc 'tam_giu')
                    'updated_at' => Carbon::now(), // Cập nhật thời gian nếu có cột
                ]);

            // Kiểm tra xem số lượng ghế được cập nhật có khớp không
            if ($updatedRows !== count($selectedSeatIds)) {
                // Nếu không khớp -> có lỗi tiềm ẩn (vd: race condition dù đã check)
                DB::rollBack(); // Hủy bỏ transaction
                Log::warning('Lỗi cập nhật ghế không khớp khi đặt vé', [
                    'phieu_dat_id' => $bookingId,
                    'expected_count' => count($selectedSeatIds),
                    'updated_count' => $updatedRows
                ]);
                // Quay lại trang chọn ghế với lỗi
                return redirect()->route('dat-ve.chon-ghe', ['maChuyenDi' => $maChuyenDi])
                                ->withErrors(['seat_error' => 'Đã có lỗi xảy ra trong quá trình giữ ghế. Vui lòng thử lại.']);
            }

            // Nếu mọi thứ thành công -> commit transaction
            DB::commit();

            // Xóa dữ liệu đặt vé khỏi session
            Session::forget('booking_data');
            // Lưu ID phiếu đặt vừa tạo vào session để dùng ở trang thành công
            Session::put('last_booked_ticket_id', $bookingId);

            // Tùy chọn: Gửi email xác nhận (nên dùng Queue để không làm chậm phản hồi)
            // try {
            //     Mail::to($validatedCustomer['email'])->queue(new BookingConfirmationMail($bookingId));
            // } catch (\Exception $mailException) {
            //     Log::error('Lỗi gửi email xác nhận đặt vé: ' . $mailException->getMessage());
            //     // Không cần rollback transaction vì vé đã đặt thành công
            // }

            // Chuyển hướng đến trang thông báo thành công
            return redirect()->route('dat-ve.thanh-cong');

        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try -> rollback transaction
            DB::rollBack();
            // Ghi log lỗi chi tiết
            Log::error('Lỗi Exception khi xử lý đặt vé: ' . $e->getMessage(), [
                 'exception' => $e,
                 'request_data' => $request->all(),
                 'session_data' => $bookingData ?? 'Không có dữ liệu session'
            ]);
            // Quay lại trang nhập thông tin với thông báo lỗi chung
            return redirect()->route('dat-ve.chi-tiet')
                            ->withInput($request->except(['_token', 'terms'])) // Giữ lại input cũ (trừ token và checkbox)
                            ->withErrors(['booking_error' => 'Đã có lỗi hệ thống xảy ra trong quá trình đặt vé. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.']);
        }
    }

    /**
     * Bước 5: Hiển thị trang thông báo đặt vé thành công.
     * Route: dat-ve.thanh-cong (GET)
     */
    public function showSuccessPage()
    {
        // Lấy ID phiếu đặt cuối cùng từ session
        $lastBookingId = Session::get('last_booked_ticket_id');
        $bookingDetails = null; // Khởi tạo biến chi tiết vé

        if ($lastBookingId) {
            // Nếu có ID, truy vấn thông tin chi tiết của phiếu đặt đó
            $bookingDetails = DB::table('phieudatxe as pdx')
                ->join('chuyendi as cd', 'pdx.MaChuyenDi', '=', 'cd.MaChuyenDi')
                ->join('lotrinh as lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
                // Left join để không bị lỗi nếu MaDiemDon/Tra bị null hoặc không tìm thấy
                ->leftJoin('diemdontra as ddt_don', 'pdx.MaDiemDon', '=', 'ddt_don.MaDiemDonTra')
                ->leftJoin('diemdontra as ddt_tra', 'pdx.MaDiemTra', '=', 'ddt_tra.MaDiemDonTra')
                ->select(
                    'pdx.MaPhieuDat',
                    'pdx.TenKhachHang', 'pdx.SoDienThoai', 'pdx.Email',
                    'lt.TenLoTrinh', 'cd.ThoiGianKhoiHanh',
                    'pdx.DanhSachTenGhe', // Lấy danh sách tên ghế đã lưu
                    'ddt_don.TenDiem as TenDiemDon', 'pdx.GhiChuDiemDon',
                    'ddt_tra.TenDiem as TenDiemTra', 'pdx.GhiChuDiemTra',
                    'pdx.TongSoTien',
                    'pdx.TrangThaiThanhToan',
                    'pdx.HinhThucThanhToan'
                )
                ->where('pdx.MaPhieuDat', $lastBookingId) // Lọc theo ID đã lưu
                ->first();
             // Cân nhắc xóa session ID sau khi đã lấy thông tin
             // Session::forget('last_booked_ticket_id');
        }

        // Trả về view thành công, truyền chi tiết vé (có thể là null)
        return view('bookticket.dat-ve-thanh-cong', compact('bookingDetails'));
    }
}