<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Tour;

class RecommendationController extends Controller
{
  private $apiBaseUrl;

  public function __construct()
  {
    $this->apiBaseUrl = env('RECOMMENDATION_API_URL', 'http://localhost:5555/api');
  }

  /**
   * Gợi ý tour cho trang chủ (Home)
   * - Nếu chưa đăng nhập: Dựa trên Cookie lịch sử xem
   * - Nếu đã đăng nhập: Dựa trên lịch sử đặt tour + đánh giá
   */
  public function homeRecommendations(Request $request)
  {
    try {
      $tourIds = [];

      // === TH1: Người dùng đã đăng nhập ===
      if (Auth::guard('client')->check()) {
        $userId = Auth::guard('client')->id();

        $response = Http::timeout(10)->get("{$this->apiBaseUrl}/user-recommendations", [
          'user_id' => $userId,
          'limit' => 4
        ]);

        if ($response->successful()) {
          $data = $response->json();
          $tourIds = $data['recommended_tours'] ?? [];
        }
      }
      // === TH2: Chưa đăng nhập => Dựa trên Cookie ===
      else {
        // Lấy lịch sử xem từ Cookie
        $viewedTours = $this->getViewedToursFromCookie($request);

        // Nếu có lịch sử. Gọi API Python
        if (!empty($viewedTours)) {
          // tour vừa xem gần nhất nằm ở đầu mảng
          $lastViewedTourId = $viewedTours[0];

          $response = Http::timeout(10)->get("{$this->apiBaseUrl}/tour-recommendations", [
            'tour_id' => $lastViewedTourId,
            'limit' => 4
          ]);

          if ($response->successful()) {
            $data = $response->json();
            $tourIds = $data['related_tours'] ?? [];
          }
        }
      }

      // Nếu danh sách ID rỗng (Do khách mới, hoặc API lỗi, hoặc không tìm thấy tour phù hợp). Trả về rỗng
      if (empty($tourIds)) {
        return response()->json([
          'success' => true,
          'tours' => [],
          // Báo về 0 để JS ẩn giao diện
          'count' => 0,
          'message' => 'Không có gợi ý phù hợp'
        ]);
      }

      // Nếu có ID thì lấy chi tiết tour từ DB
      $tours = $this->getToursWithDetails($tourIds);

      return response()->json([
        'success' => true,
        'tours' => $tours,
        'count' => count($tours)
      ]);
    } catch (\Exception $e) {
      Log::error('Home Recommendations Error: ' . $e->getMessage());
      // Gặp lỗi hệ thống cũng trả về rỗng để ẩn đi
      return response()->json([
        'success' => false,
        'tours' => [],
        'count' => 0
      ]);
    }
  }

  /**
   * Gợi ý tour tương tự trong trang chi tiết tour
   */
  public function similarTours($tourId)
  {
    try {
      $response = Http::timeout(10)->get("{$this->apiBaseUrl}/tour-recommendations", [
        'tour_id' => $tourId,
        'limit' => 4
      ]);

      if ($response->successful()) {
        $data = $response->json();
        $tourIds = $data['related_tours'] ?? [];

        if (!empty($tourIds)) {
          $tours = $this->getToursWithDetails($tourIds);

          return response()->json([
            'success' => true,
            'tours' => $tours
          ]);
        }
      }

      return response()->json([
        'success' => false,
        'message' => 'Không tìm thấy tour tương tự'
      ]);
    } catch (\Exception $e) {
      Log::error('Similar Tours Error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi khi lấy gợi ý'
      ], 500);
    }
  }

  /**
   * Gợi ý tour dựa trên từ khóa tìm kiếm
   */
  public function searchRecommendations(Request $request)
  {
    $keyword = $request->input('keyword');

    if (empty($keyword)) {
      return response()->json([
        'success' => false,
        'message' => 'Thiếu từ khóa'
      ], 400);
    }

    try {
      $response = Http::timeout(10)->get("{$this->apiBaseUrl}/search-tours", [
        'keyword' => $keyword,
        'limit' => 4
      ]);

      if ($response->successful()) {
        $data = $response->json();
        $tourIds = $data['related_tours'] ?? [];

        if (!empty($tourIds)) {
          $tours = $this->getToursWithDetails($tourIds);

          return response()->json([
            'success' => true,
            'tours' => $tours,
            'keyword' => $keyword
          ]);
        }
      }

      return response()->json([
        'success' => true,
        'tours' => [],
        'message' => 'Không tìm thấy tour phù hợp với từ khóa'
      ]);
    } catch (\Exception $e) {
      Log::error('Search Recommendations Error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi khi tìm kiếm'
      ], 500);
    }
  }

  /**
   * Lưu tour đã xem vào Cookie
   */
  public function trackTourView(Request $request)
  {
    $tourId = $request->input('tour_id');

    if (!$tourId) {
      return response()->json(['success' => false]);
    }

    // Lấy danh sách tour đã xem từ cookie
    $viewedTours = $this->getViewedToursFromCookie($request);

    // Thêm tour mới vào đầu mảng (nếu chưa có)
    if (!in_array($tourId, $viewedTours)) {
      array_unshift($viewedTours, $tourId);
    }

    // Giữ tối đa 10 tour gần nhất
    $viewedTours = array_slice($viewedTours, 0, 10);

    // Lưu vào cookie (30 ngày)
    $cookie = cookie('viewed_tours', json_encode($viewedTours), 43200); // 43200 phút = 30 ngày

    return response()->json(['success' => true])->cookie($cookie);
  }

  /**
   * Lấy danh sách tour đã xem từ Cookie
   */
  private function getViewedToursFromCookie(Request $request)
  {
    $viewedTours = $request->cookie('viewed_tours');

    if ($viewedTours) {
      $decoded = json_decode($viewedTours, true);
      return is_array($decoded) ? $decoded : [];
    }

    return [];
  }

  /**
   * Lấy thông tin chi tiết tour theo danh sách ID
   */
  private function getToursWithDetails($tourIds)
  {
    if (empty($tourIds)) {
      return [];
    }

    $tours = Tour::whereIn('tour_id', $tourIds)
      ->where('status', 'active')
      ->with(['villages.province.region', 'schedules'])
      ->get()
      ->map(function ($tour) {
        return [
          'tour_id' => $tour->tour_id,
          'tour_name' => $tour->tour_name,
          'price_adult' => $tour->price_adult,
          'price_child' => $tour->price_child,
          'duration' => $tour->duration,
          'destination' => $tour->destination,
          'departure_point' => $tour->departure_point,
          'transport' => $tour->transport,
          'image_main' => $tour->image_main,
          'url' => route('tour.detail', $tour->tour_id),
          'schedules' => $tour->schedules->map(function ($schedule) {
            return [
              'start_date' => $schedule->start_date->format('d/m'),
              'end_date' => $schedule->end_date->format('d/m/Y'),
            ];
          })
        ];
      });

    // Sắp xếp theo thứ tự gợi ý từ Python
    $sortedTours = collect($tourIds)->map(function ($id) use ($tours) {
      return $tours->firstWhere('tour_id', $id);
    })->filter();

    return $sortedTours->values()->toArray();
  }
}
