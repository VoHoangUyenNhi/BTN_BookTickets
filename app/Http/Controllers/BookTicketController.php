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
                ->select(
                    'cd.MaChuyenDi',
                    'dd.DiemDi',
                    'dd.DiemDen',
                    'lt.TenLoTrinh',
                    'cd.ThoiGianKhoiHanh',
                    'lx.TenLoaiXe',
                    'cd.loaive',
                    'bg.GiaNgayThuong',
                    'sg.SoGheTrong'
                )
                ->join('lotrinh as lt', 'cd.MaLoTrinh', '=', 'lt.MaLoTrinh')
                ->joinSub(
                    DB::table('diemdiqua')
                        ->select('MaLoTrinh', DB::raw('MIN(TenDiaDiem) AS DiemDi'), DB::raw('MAX(TenDiaDiem) AS DiemDen'))
                        ->groupBy('MaLoTrinh'),
                    'dd',
                    'lt.MaLoTrinh',
                    '=',
                    'dd.MaLoTrinh'
                )
                ->join('banggia as bg', 'cd.MaGia', '=', 'bg.MaGia')
                ->join('loaixe as lx', 'bg.MaLoaiXe', '=', 'lx.MaLoaiXe')
                ->leftJoinSub(
                    DB::table('xeghe as xg')
                        ->select(DB::raw('COUNT(xg.MaXeGhe) AS SoGheTrong'), 'x.MaLoaiXe')
                        ->join('xe as x', 'xg.MaXe', '=', 'x.MaXe')
                        ->join('maughetieuchuan as mgtc', function ($join) {
                            $join->on('mgtc.MaLoaiXe', '=', 'x.MaLoaiXe')
                                ->on('mgtc.MaMauGheTieuChuan', '=', 'xg.MaMauGheTieuChuan');
                        })
                        ->where('xg.TrangThai', 'trống')
                        ->groupBy('x.MaLoaiXe'),
                    'sg',
                    'sg.MaLoaiXe',
                    '=',
                    'lx.MaLoaiXe'
                )
                ->whereDate('cd.ThoiGianKhoiHanh', $ngayDi)
                ->where('dd.DiemDi', $diemDi)
                ->where('dd.DiemDen', $diemDen)
                ->where('cd.loaive', 'Một chiều')
                ->get();

            return view('bookticket.ketquatimkiem', compact('trips'));
        }
}
