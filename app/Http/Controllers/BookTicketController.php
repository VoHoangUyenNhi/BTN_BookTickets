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
}
