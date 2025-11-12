<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Tour;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function index()
  {
    // Tổng quan
    $totalUsers = User::count();
    $totalTours = Tour::where('status', 'approved')->count();

    // Chỉ đếm các booking đã hoàn tất
    $totalBookings = Booking::where('status', 'complete')->count();

    // Tổng doanh thu (booking "paid" + payment "success")
    $totalRevenue = Booking::join('payments', 'bookings.booking_id', '=', 'payments.booking_id')
      ->where('bookings.status', 'paid')
      ->where('payments.status', 'success')
      ->sum('bookings.total_price');

    return view('admin.dashboard', compact(
      'totalUsers',
      'totalTours',
      'totalBookings',
      'totalRevenue'
    ));
  }

  // Doanh thu theo tháng (12 tháng gần nhất)
  public function getMonthlyRevenue()
  {
    $data = Booking::join('payments', 'bookings.booking_id', '=', 'payments.booking_id')
      ->select(
        DB::raw('MONTH(bookings.created_at) as month'),
        DB::raw('SUM(bookings.total_price) as revenue')
      )
      ->where('bookings.status', 'paid')
      ->where('payments.status', 'success')
      ->whereYear('bookings.created_at', Carbon::now()->year)
      ->groupBy('month')
      ->orderBy('month')
      ->get();

    return response()->json($data);
  }

  // Doanh thu theo tuần
  public function getWeeklyRevenue()
  {
    $data = Booking::join('payments', 'bookings.booking_id', '=', 'payments.booking_id')
      ->select(
        DB::raw('YEAR(bookings.created_at) as year'),
        DB::raw('WEEK(bookings.created_at, 1) as week'),
        DB::raw('SUM(bookings.total_price) as revenue')
      )
      ->where('bookings.status', 'paid')
      ->where('payments.status', 'success')
      ->whereYear('bookings.created_at', Carbon::now()->year)
      ->groupBy('year', 'week')
      ->orderBy('year')
      ->orderBy('week')
      ->get();

    return response()->json($data);
  }

  // Doanh thu theo ngày (7 ngày gần nhất)
  public function getDailyRevenue()
  {
    $data = Booking::join('payments', 'bookings.booking_id', '=', 'payments.booking_id')
      ->select(
        DB::raw('DATE(bookings.created_at) as date'),
        DB::raw('SUM(bookings.total_price) as revenue')
      )
      ->where('bookings.status', 'paid')
      ->where('payments.status', 'success')
      ->whereBetween('bookings.created_at', [now()->subDays(6), now()])
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    return response()->json($data);
  }

  // Thống kê trạng thái đơn hàng
  public function getOrderStatus()
  {
    $data = Booking::select('status', DB::raw('COUNT(*) as count'))
      ->groupBy('status')
      ->get();

    return response()->json($data);
  }

  // Top 5 tour có doanh thu cao nhất
  public function getTopTours()
  {
    $data = Booking::join('payments', 'bookings.booking_id', '=', 'payments.booking_id')
      ->join('tours', 'bookings.tour_id', '=', 'tours.tour_id')
      ->select(
        'tours.tour_name as label',
        DB::raw('SUM(bookings.total_price) as value')
      )
      ->where('bookings.status', 'paid')
      ->where('payments.status', 'success')
      ->groupBy('tours.tour_name')
      ->orderByDesc('value')
      ->limit(5)
      ->get();

    return response()->json($data);
  }

  // Top 5 tour được đặt nhiều nhất (booking complete)
  public function getTopBookedTours()
  {
    $data = Booking::join('tours', 'bookings.tour_id', '=', 'tours.tour_id')
      ->select(
        'tours.tour_name as label',
        DB::raw('COUNT(bookings.booking_id) as value')
      )
      ->where('bookings.status', 'complete')
      ->groupBy('tours.tour_name')
      ->orderByDesc('value')
      ->limit(1)
      ->get();

    return response()->json($data);
  }

  // Tỷ lệ phương thức thanh toán
  public function getPaymentMethods()
  {
    $data = Payment::select('payment_method as label', DB::raw('COUNT(*) as value'))
      ->where('status', 'success')
      ->groupBy('payment_method')
      ->get();

    return response()->json($data);
  }
}
