<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class AccountController extends Controller
{

// Xem thông tin tài khoản
function profileinfo()
{
$user = DB::table("users")->whereRaw("id=?",[Auth::user()->id])->first();
return view("bookticket.profile",compact("user"));
}

// Chỉnh sửa lại thông tin
public function account()
{
    $user = DB::table("users")->where("id", Auth::id())->first();
    return view("bookticket.account", compact("user"));
}

// Lưu thông tin
function saveaccountinfo(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string'],
            'photo' => ['nullable','image'],
            'date' => ['nullable','date'],
            'gender' => ['nullable', 'in:Nam,Nữ,Khác'],  // chỉ cho phép 3 giá trị
            'career' => ['nullable', 'string', 'max:255']
        ]);

        $id = $request->input('id');
        $data["name"] = $request->input("name");
        $data["phone"] = $request->input("phone");
        $data["email"] = $request->input("email");
        if($request->hasFile("photo"))
        {
            //Tạo tên file bằng cách lấy id của người dùng ghép với phần mở rộng của hình ảnh
            $fileName = Auth::user()->id . '.' . $request->file('photo')->extension();
            //File được lưu vào thư mục storage/app/public/profile
            $request->file('photo')->storeAs('public/profile', $fileName);
            $data['photo'] = $fileName;
        }
        $data["date"] = $request->input("date");
        $data["gender"] = $request->input("gender");
        $data["career"] = $request-> input("career");
        DB::table("users")->where("id", $id)->update($data);

        return redirect()->route('account')->with('status', 'Cập nhật thành công');
    }
}