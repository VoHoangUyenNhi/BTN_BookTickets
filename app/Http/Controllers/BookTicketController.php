<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookTicketController extends Controller
{
    public function index()
    {
        // Lấy danh sách tên địa điểm từ bảng diem_di_qua
        $locations = DB::table('diemdiqua')->distinct()->pluck('TenDiaDiem');

        return view('bookticket.index', compact('locations'));
    }
    public function showForm()
    {
        return view('bookticket.tracuuve');
    }

    public function searchTicket(Request $request)
    {
        $request->validate([
            'sodienthoai' => 'required',
            'mave' => 'required',
        ]);

        $ticket = DB::table('phieudatxe')
            ->join('khachhang', 'phieudatxe.MaKhachHang', '=', 'khachhang.MaKhachHang')
            ->join('chuyendi', 'phieudatxe.MaChuyenDi', '=', 'chuyendi.MaChuyenDi')
            ->join('lotrinh', 'chuyendi.MaLoTrinh', '=', 'lotrinh.MaLoTrinh')
            ->where('khachhang.SoDienThoai', $request->sodienthoai)
            ->where('phieudatxe.MaPhieuDat', $request->mave)
            ->select(
                'khachhang.HoTen',
                'khachhang.SoDienThoai',
                'phieudatxe.MaPhieuDat',
                'lotrinh.TenLoTrinh',
                'chuyendi.ThoiGianKhoiHanh',
                'phieudatxe.SoGhe',
                'phieudatxe.TongSoTien'
            )
            ->first();

        if ($ticket) {
            return view('bookticket.tracuuve', ['ticket' => $ticket]);
        } else {
            return back()->with('error', 'Không tìm thấy thông tin vé.');
        }
    }


}
