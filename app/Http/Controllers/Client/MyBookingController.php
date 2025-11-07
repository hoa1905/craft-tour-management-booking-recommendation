<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MyBookingController extends Controller
{
    // Hiển thị trang chính
    public function index(Request $request)
    {
        $client = Auth::guard('client')->user();
        $status = $request->get('status', 'all');

        $bookings = Booking::ofUser($client->user_id)
            ->with(['tour', 'schedule', 'payment'])
            ->filterByStatus($status)
            ->latest() // tương đương với ->orderBy('created_at', 'DESC')
            ->get();

        return view('client.my_booking', compact('bookings', 'status'));
    }

    /**
     * Hiển thị chi tiết một đơn đặt tour
     */
    public function show(Booking $booking)
    {
        // Tải các quan hệ cần thiết (giống hệt hàm index)
        $booking->load(['tour', 'schedule', 'payment']);

        // Trả về view
        return view('client.my_booking_detail', compact('booking'));
    }

    /**
     * Xử lý hủy đơn tour
     */
    public function cancel(Booking $booking)
    {
        // Kiểm tra xem có được phép hủy không (dùng lại Accessor)
        if (!$booking->is_cancelable) {
            return back()->with('error', 'Tour này không còn trong điều kiện được phép hủy.');
        }

        // Xác định xem có cần hoàn tiền không
        $isPaid = $booking->payment?->status === 'success';

        try {
            DB::beginTransaction();

            // Nếu đã thanh toán chuyển sang trạng thái chờ xử lý hoàn tiền
            if ($isPaid) {
                $booking->status = 'pending_refund';
                $booking->payment->status = 'pending_refund';
                $booking->payment->save();

                $message = 'Hủy tour thành công. Yêu cầu hoàn tiền của bạn đang được xử lý.';
            } else {
                // Nếu chưa thanh toán hủy bình thường
                $booking->status = 'canceled';
                if ($booking->payment) {
                    $booking->payment->status = 'failed';
                    $booking->payment->save();
                }
                $message = 'Hủy tour thành công.';
            }

            $booking->save();
            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi hủy tour: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại.');
        }
    }
}
