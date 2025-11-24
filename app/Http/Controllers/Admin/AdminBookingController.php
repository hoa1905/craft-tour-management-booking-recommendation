<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminBookingController extends Controller
{
  /**
   * Hiển thị danh sách đơn đặt tour
   */
  public function index(Request $request)
  {
    $status = $request->input('status', 'all');

    // Eager Load: Lấy Booking kèm User, Tour, Schedule và Payment
    $query = Booking::with(['user', 'tour', 'schedule', 'payment']);

    // Lọc theo trạng thái Booking
    if ($status !== 'all') {
      $query->where('status', $status);
    }

    $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.bookings.list', compact('bookings', 'status'));
  }

  /**
   * Cập nhật trạng thái Đơn hàng
   */
  public function updateStatus(Request $request, $id)
  {
    $request->validate([
      'status' => 'required|in:waiting_payment,paid,active,completed,canceled'
    ]);

    DB::beginTransaction();
    try {
      $booking = Booking::with('payment')->findOrFail($id);
      $newStatus = $request->status;
      $message = "Cập nhật trạng thái đơn hàng thành công!";

      // Nếu chọn hủy
      if ($newStatus === 'canceled') {
        // Nếu đã có thanh toán và đã thanh toán thành công
        if ($booking->payment && $booking->payment->status === 'success') {

          // Tự động chuyển Payment sang "Chờ hoàn tiền"
          $booking->payment->status = 'pending_refund';
          $booking->payment->save();

          $message .= "Trạng thái thanh toán đã tự động chuyển sang 'Chờ hoàn tiền";
        }
      }

      // Cập nhật Booking
      $booking->status = $newStatus;
      $booking->save();

      DB::commit();
      return redirect()->back()->with('status', $message);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error("Lỗi update booking: " . $e->getMessage());
      return redirect()->back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
  }

  /**
   * Cập nhật trạng thái thanh toán
   */
  public function updatePaymentStatus(Request $request, $id)
  {
    $request->validate([
      'payment_status' => 'required|in:waiting_payment,success,failed,pending_refund,refunded'
    ]);

    try {
      // Tìm Booking, kiểm tra Payment
      $booking = Booking::with('payment')->findOrFail($id);

      // Cập nhật trạng thái Payment
      $booking->payment->status = $request->payment_status;
      $booking->payment->save();

      // Tự động chuyển trạng thái tour
      if ($request->payment_status === 'success' && $booking->status === 'waiting_payment') {
        $booking->status = 'active'; // Chuyển sang đang hoạt động
        $booking->save();

        // Có thể gửi email vé điện tử cho khách tại đây
      }

      return redirect()->back()->with('status', 'Cập nhật trạng thái thanh toán thành công!');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
    }
  }

  /**
   * Xem chi tiết đơn hàng
   * Load đầy đủ quan hệ để hiển thị ra view detail
   */
  public function detail($id)
  {
    // Sử dụng Eager Loading (with) để giảm số lượng truy vấn database
    // và lấy đầy đủ dữ liệu cho các Card trong giao diện
    $booking = Booking::with(['user', 'tour', 'schedule', 'payment'])->findOrFail($id); // Nếu không tìm thấy ID thì trả về lỗi 404

    return view('admin.bookings.detail', compact('booking'));
  }

  /**
   * In hóa đơn đặt tour (PDF)
   */
  public function invoice($id)
  {
    // Lấy Booking kèm Tour, Schedule, Payment
    $booking = Booking::with(['tour', 'schedule', 'payment', 'user'])->findOrFail($id);

    $pdf = Pdf::loadView('admin.bookings.invoice', compact('booking'))
      ->setPaper('A4', 'portrait')
      ->setOption(['defaultFont' => 'DejaVu Sans']); // Hỗ trợ tiếng Việt

    return $pdf->stream('hoa-don-tour-' . $booking->booking_id . '.pdf');
  }
}
