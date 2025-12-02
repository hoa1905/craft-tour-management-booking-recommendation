<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Tour;
use App\Models\Payment;
use App\Models\TourRequest;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function index()
  {
    // --- Tổng quan ---
    $totalUsers = User::where('role', 'client')->count();
    $totalTours = Tour::where('status', 'active')->count();
    // Đếm booking Tour đã thực hiện
    $totalBookings = Booking::where('status', 'completed')->count();

    // Doanh thu: Booking là paid + active + completed và Payment là success
    $totalRevenue = Booking::whereHas('payment', fn($q) => $q->where('status', 'success'))
      ->whereIn('status', ['paid', 'active', 'completed'])
      ->sum('total_price');

    // --- Cần xử lý và theo dõi ---
    $pendingRequests = TourRequest::where('status', 'pending')->count();

    // Tổng Làng Nghề
    $totalVillages = Village::where('status', 'active')->count();

    // Tour sắp khởi hành (7 ngày tới)
    $upcomingTours = Booking::where('status', 'active')
      ->whereHas('schedule', fn($q) => $q->whereBetween('start_date', [now(), now()->addDays(7)]))
      ->count();

    $canceledBookings = Booking::where('status', 'canceled')->count();

    return view('admin.dashboard', compact(
      'totalUsers',
      'totalTours',
      'totalBookings',
      'totalRevenue',
      'pendingRequests',
      'totalVillages',
      'upcomingTours',
      'canceledBookings'
    ));
  }
  // --- Truy vấn bằng Eloquent (khi trong các Model đã khai báo quan hệ) ---
  // 1. Doanh thu theo tháng
  public function getMonthlyRevenue()
  {
    $data = Booking::selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
      ->whereHas('payment', fn($q) => $q->where('status', 'success'))
      ->whereIn('status', ['paid', 'active', 'completed']) // Lấy cả 3 trạng thái hợp lệ
      ->whereYear('created_at', Carbon::now()->year)
      ->groupBy('month')
      ->orderBy('month')
      ->get();

    return response()->json($data);
  }

  // 2. Doanh thu theo tuần
  public function getWeeklyRevenue()
  {
    $data = Booking::selectRaw('WEEK(created_at, 1) as week, SUM(total_price) as revenue')
      ->whereHas('payment', fn($q) => $q->where('status', 'success'))
      ->whereIn('status', ['paid', 'active', 'completed'])
      ->whereYear('created_at', Carbon::now()->year)
      ->groupBy('week')
      ->orderBy('week')
      ->get();

    return response()->json($data);
  }

  // 3. Doanh thu theo ngày (7 ngày gần nhất)
  public function getDailyRevenue()
  {
    $data = Booking::selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
      ->whereHas('payment', fn($q) => $q->where('status', 'success'))
      ->whereIn('status', ['paid', 'active', 'completed'])
      ->whereBetween('created_at', [now()->subDays(6), now()])
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    return response()->json($data);
  }

  // 4. Thống kê trạng thái
  public function getOrderStatus()
  {
    $data = Booking::select('status', DB::raw('count(*) as count'))
      ->groupBy('status')
      ->get();

    return response()->json($data);
  }

  // 5. Top 5 Tour Doanh thu cao nhất (Eloquent Relations)
  public function getTopTours()
  {
    $data = Tour::withSum(['bookings' => function ($q) {
      $q->whereHas('payment', fn($p) => $p->where('status', 'success'))
        ->whereIn('status', ['paid', 'active', 'completed']);
    }], 'total_price')
      ->orderByDesc('bookings_sum_total_price')
      ->take(5)
      ->get()
      ->map(function ($tour) {
        return [
          'label' => $tour->tour_name,
          'value' => $tour->bookings_sum_total_price ?? 0 // Trả về 0 nếu null
        ];
      });

    return response()->json($data);
  }

  // 6. Top 5 Tour được đặt nhiều nhất (Sửa lỗi không hiện)
  public function getTopBookedTours()
  {
    // Đếm số lượng booking thành công của từng tour
    $data = Tour::withCount(['bookings' => function ($query) {
      // Chỉ đếm các đơn đã xác nhận/thanh toán/hoàn thành
      $query->whereIn('status', ['paid', 'active', 'completed']);
    }])
      ->orderByDesc('bookings_count')
      ->take(5)
      ->get()
      // Filter bỏ những tour chưa ai đặt (count = 0)
      ->filter(function ($tour) {
        return $tour->bookings_count > 0;
      })
      ->map(function ($tour) {
        return [
          'label' => $tour->tour_name,
          'value' => $tour->bookings_count
        ];
      })
      ->values(); // Reset index mảng

    return response()->json($data);
  }

  // 7. Phương thức thanh toán
  public function getPaymentMethods()
  {
    $data = Payment::select('payment_method as label', DB::raw('count(*) as value'))
      ->where('status', 'success')
      ->groupBy('payment_method')
      ->get();

    return response()->json($data);
  }
}
