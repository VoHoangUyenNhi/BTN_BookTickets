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
}
