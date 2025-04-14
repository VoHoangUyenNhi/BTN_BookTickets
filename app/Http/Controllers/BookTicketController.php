<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

class BookTicketController extends Controller
{
    public function index()
    {
        // Lấy danh sách tên địa điểm từ bảng diem_di_qua
        $locations = DB::table('diemdiqua')->distinct()->pluck('TenDiaDiem');

        return view('bookticket.index', compact('locations'));
    }
    // Nhớ chạy lệnh composer require endroid/qr-code trong cmd
    public function testSendEmail(Request $request)
    {
        // Lấy thông tin vé và tên khách hàng
        $ticketInfo = $request->input('ticket_info');
        $customerName = $request->input('customer_name');
        
        // Tạo mã QR
        $qrCode = new QrCode($ticketInfo);
        $qrCode->setSize(200);

        // Tạo writer và xuất mã QR dưới dạng chuỗi PNG
        $qrCode = new QrCode($ticketInfo);
        $qrCode->setEncoding(new Encoding('UTF-8'));
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode)->getString();
       
        // Gửi email
        Mail::send([], [], function ($message) use ($qrCodeImage, $customerName) {
            $message->to(Auth::user()->email) // Địa chỉ email người nhận
                ->subject('Cảm ơn bạn đã đặt vé!')
                ->html("
                    <div style=\"font-family: Arial, sans-serif; color: #333; background-color: #fff0f5; padding: 30px;\">
                        <div style=\"max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);\">
                            <div style=\"background-color: #ff69b4; padding: 20px; text-align: center; color: #fff;\">
                                <h2 style=\"margin: 0;\">💖 Cảm ơn bạn đã đặt vé!</h2>
                            </div>
                            <div style=\"padding: 30px;\">
                                <p style=\"font-size: 16px;\">Xin chào <strong style=\"color: #ff69b4;\">{$customerName}</strong>,</p>
                                <p style=\"font-size: 16px;\">Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi.</p>
                                <p style=\"font-size: 16px;\">Vé điện tử của bạn đã được tạo thành công. Vui lòng xuất trình mã QR bên dưới khi lên xe để kiểm tra.</p>
                                
                                <div style=\"text-align: center; margin: 30px 0;\">
                                <img src=\"".asset('/giphy.gif')."\" alt=\"Cute Bus\" style=\"width: 150px; height: auto; border-radius: 8px;\" />
<p style=\"font-size: 14px; color: #999;\">(Chiếc xe dễ thương này sẽ chở bạn đi muôn nơi 🚌💨)</p>

                                </div>

                                <p style=\"font-size: 16px;\">Nếu bạn có bất kỳ câu hỏi nào hoặc cần hỗ trợ, đừng ngần ngại liên hệ với chúng tôi.</p>
                                <p style=\"font-size: 16px;\">💝 Chúc bạn có một chuyến đi an toàn và vui vẻ!</p>

                                <p style=\"margin-top: 30px;\">Trân trọng,<br><strong>Đội ngũ hỗ trợ khách hàng</strong></p>
                            </div>
                            <div style=\"background-color: #ffe4ea; padding: 15px; text-align: center; font-size: 12px; color: #999;\">
                                ©  2025 Bao Toan Car. All rights reserved.
                            </div>
                        </div>
                    </div>
                ");
            // Đính kèm mã QR
            $message->attachData($qrCodeImage, 'ticket_qr.png', [
                'mime' => 'image/png',
            ]);
        });

        return response()->json(['message' => 'Email đã được gửi thành công!']);
    }
}