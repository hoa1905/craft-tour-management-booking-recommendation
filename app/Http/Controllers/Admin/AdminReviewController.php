<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminReviewController extends Controller
{
  public function index(Request $request)
  {
    // Khởi tạo Query với Eager Loading
    $query = Review::with(['user', 'tour'])->orderBy("created_at", "DESC");

    // Lọc theo số sao
    if ($request->filled('rating') && $request->rating != 'default') {
      $query->where('rating', $request->rating);
    }

    $reviews = $query->paginate(10)->withQueryString();

    return view('admin.reviews.list', compact('reviews'));
  }

  public function delete($id)
  {
    $review = Review::find($id);

    // Kiểm tra xem bài đánh giá có ảnh không
    if (!empty($review->images) && is_array($review->images)) {
      foreach ($review->images as $imageName) {
        // Tạo đường dẫn tuyệt đối đến file ảnh
        $imagePath = public_path('images/reviews/' . $imageName);

        // Kiểm tra file có tồn tại không thì mới xóa
        if (File::exists($imagePath)) {
          File::delete($imagePath);
        }
      }
    }

    // Sau khi xóa ảnh xong thì xóa dữ liệu trong DB
    $review->delete();

    return redirect()->route('admin.review.index')
      ->with('status', 'Xóa đánh giá thành công!');
  }
}
