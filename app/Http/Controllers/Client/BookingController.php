<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        // Lưu dữ liệu booking vào session
        session(['booking_data' => $request->all()]);

        return response()->json([
            'success' => true,
            'message' => 'Chuyển đến trang đặt tour...',
            'redirect_url' => route('booking'),
        ]);
    }

    public function checkout()
    {
        // Lấy dữ liệu từ session
        $bookingData = session('booking_data');

        if (!$bookingData) {
            return redirect()->route('home')->with('error', 'Không có dữ liệu đặt tour!');
        }

        return view('client.booking', compact('bookingData'));
    }

    public function listBookings()
    {
        return view('client.home');
    }
}
