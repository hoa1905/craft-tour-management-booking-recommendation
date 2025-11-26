<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

  public function loadReviews($tour_id)
  {
    $reviews = Review::with('user')
      ->where('tour_id', $tour_id)
      ->orderByDesc('review_id')
      ->paginate(10);

    // Tạo HTML cho danh sách
    $html = "";
    foreach ($reviews as $review) {

      $avatar = $review->user->avatar
        ? asset('images/client/avatar/' . $review->user->avatar)
        : null;

      $html .= '<li class="list-group-item">
            <div style="display:flex;align-items:flex-start;gap:15px;">';

      // Avatar
      if ($avatar) {
        $html .= '<img src="' . $avatar . '" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">';
      } else {
        $html .= '<div style="width:50px;height:50px;border-radius:50%;background:#ddd;
                        display:flex;align-items:center;justify-content:center;font-size:22px;">
                        <i class="fa fa-user"></i></div>';
      }

      // Nội dung review
      $html .= '<div style="flex:1;">
                    <strong>' . $review->user->user_name . '</strong><br>
                    <span class="rated">' . str_repeat("★", $review->rating) . '</span>
                    <small class="text-muted">' . $review->created_at->format('d/m/Y H:i') . '</small>
                    <p>' . $review->comment . '</p>';

      // Hình ảnh
      if (!empty($review->images)) {
        $html .= '<div class="row">';
        foreach ($review->images as $img) {
          $html .= '
                    <div class="col-xs-4">
                        <img src="' . asset('images/reviews/' . $img) . '"
                        class="img-responsive img-thumbnail"
                        style="max-height:120px;">
                    </div>';
        }
        $html .= '</div>';
      }

      $html .= '</div></div></li>';
    }

    // HTML phân trang
    $pagination = $reviews->links()->render();

    return response()->json([
      'html' => $html,
      'pagination' => $pagination
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'tour_id' => 'required|integer',
      'rating'  => 'required|integer|min:1|max:5',
      'comment' => 'nullable|string|max:255',
      'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ], [
      'rating.required' => 'Vui lòng chọn số sao.',
    ]);

    // Kiểm tra khách đã du lịch tour hoàn thành
    $hasBooking = Booking::where('user_id', Auth::guard('client')->id())
      ->where('tour_id', $request->tour_id)
      ->where('status', 'completed')
      ->exists();

    if (!$hasBooking) {
      return response()->json([
        'success' => false,
        'message' => 'Bạn phải trải nghiệm du lịch tour mới có thể đánh giá.'
      ], 403);
    }

    // Xử lý upload ảnh
    $imageList = [];

    if ($request->hasFile('images')) {
      if (count($request->file('images')) > 5) {
        return response()->json([
          'success' => false,
          'message' => 'Bạn chỉ được chọn tối đa 5 ảnh.'
        ], 422);
      }

      foreach ($request->file('images') as $file) {
        $fileName = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
        $file->move(public_path('images/reviews'), $fileName);
        $imageList[] = $fileName;
      }
    }

    // Lưu vào review (JSON)
    $review = Review::create([
      'tour_id' => $request->tour_id,
      'user_id' => Auth::guard('client')->id(),
      'rating'  => $request->rating,
      'comment' => $request->comment,
      'images'  => $imageList, // Mảng JSON
    ]);

    return response()->json([
      'success' => true,
      'data' => $review
    ]);
  }
}
