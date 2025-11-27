@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@section('content')
<div class="container-fluid py-4">
  @if(session('status'))
  <div class="alert custom-alert alert-success alert-dismissible fade show border-0 shadow" role="alert" id="status-alert">
    <div class="d-flex align-items-center">
      <div class="alert-icon-container me-3">
        <i class="fas fa-check-circle"></i>
      </div>
      <div>
        <p class="mb-0">{{ session('status') }}</p>
      </div>
    </div>
    <div class="progress mt-2" style="height: 3px;">
      <div id="alert-progress-bar" class="progress-bar bg-white" style="width: 100%;"></div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <div class="card shadow border-0 rounded-3 overflow-hidden">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
      <h3 class="mb-0 d-flex align-items-center text-primary">
        <i class="bi bi-star-fill text-primary me-2 fs-4"></i>
        Quản Lý đánh giá
      </h3>
    </div>

    <!-- Thanh tìm kiếm và lọc -->
    <div class="card-body bg-light py-3">
      <form action="{{ route('admin.review.index') }}" method="GET">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input type="text" class="form-control border-start-0" placeholder="Tìm theo tên khách hàng hoặc tour" onkeyup="filterTable()">
            </div>
          </div>
          <div class="col-md-3">
            <select name="rating" class="form-select" onchange="this.form.submit()">
              <option value="default">Tất cả đánh giá</option>
              <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 sao</option>
              <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 sao</option>
              <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 sao</option>
              <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 sao</option>
              <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 sao</option>
            </select>
          </div>
        </div>
      </form>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="reviewTable">
          <thead class="bg-light">
            <tr>
              <th scope="col" class="ps-4 py-3" width="5%">STT</th>
              <th scope="col" class="ps-4 py-3" width="15%">Khách hàng</th>
              <th scope="col" class="py-3" width="20%">Tour</th>
              <th scope="col" class="py-3 text-center" width="5%">Đánh Giá</th>
              <th scope="col" class="py-3 text-center" width="10%">Hình Ảnh</th>
              <th scope="col" class="py-3" width="20%">Bình Luận</th>
              <th scope="col" class="py-3 text-center" width="15%">Ngày Đánh Giá</th>
              <th scope="col" class="py-3 text-center" width="10%">Thao tác</th>
            </tr>
          </thead>
          <tbody id="reviewTableBody">
            @forelse ($reviews as $key => $review)
            <tr class="{{ $key % 2 == 0 ? 'bg-white' : 'bg-light-subtle' }}" id="review-row-{{ $review->review_id }}">
              <td class="text-center align-middle">
                {{ $reviews->firstItem() + $loop->index }}
              </td>
              <td class="ps-4">
                <div class="d-flex align-items-center">
                  <div class="position-relative">
                    <img src="{{ asset('images/client/avatar/' . $review->user->avatar) }}"
                      alt="Avatar"
                      class="rounded-circle border  border-primary shadow-sm"
                      style="width: 45px; height: 45px; object-fit: cover;">
                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1 border border-white"
                      style="width: 12px; height: 12px;"></span>
                  </div>
                  <div class="ms-3">
                    <h6 class="mb-0 fw-semibold">{{ $review->user->user_name }}</h6>
                    <small class="text-muted">Mã KH: {{ $review->user->user_id }}</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <img src="{{ asset('images/tours/' . $review->tour->image_main) }}"
                    alt="Hình ảnh tour"
                    class="rounded shadow-sm border"
                    style="width: 40px; height: 40px; object-fit: cover;">
                  <div class="ms-3">
                    <h6 class="mb-0 text-truncate">{{ $review->tour->tour_name }}</h6>
                    <small class="text-muted">Mã tour: {{ $review->tour->tour_id }}</small>
                  </div>
                </div>
              </td>
              <td class="text-center">
                <div class="d-flex justify-content-center">
                  @for ($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary' }} mx-1"></i>
                    @endfor
                </div>
                <span class="badge bg-{{ $review->rating >= 4 ? 'success' : ($review->rating >= 3 ? 'warning' : 'danger') }} mt-1">
                  {{ $review->rating }}/5
                </span>
              </td>
              <td class="text-center">
                {{-- Kiểm tra xem có ảnh và mảng có phần tử không --}}
                @if ($review->images && count($review->images) > 0)

                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $review->review_id }}">
                  {{-- Lấy phần tử đầu tiên của mảng: $review->images[0] --}}
                  <img src="{{ asset('images/reviews/' . $review->images[0]) }}"
                    alt="Hình ảnh đánh giá"
                    class="rounded shadow-sm border"
                    style="width: 60px; height: 60px; object-fit: cover; transition: transform 0.2s;">

                  {{-- Nếu có nhiều hơn 1 ảnh, hiện thêm số lượng --}}
                  @if(count($review->images) > 1)
                  <span class="badge bg-primary position-absolute translate-middle border border-light rounded-circle"
                    style="font-size: 10px; padding: 3px 5px; margin-left: -10px; margin-top: -5px;">
                    +{{ count($review->images) - 1 }}
                  </span>
                  @endif
                </a>

                @else
                <span class="badge bg-secondary py-2 px-3">
                  <i class="bi bi-camera-slash me-1"></i> Không có ảnh
                </span>
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="bg-light rounded p-2 me-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-chat-quote text-primary"></i>
                  </div>
                  <span class="d-inline-block">
                    @if (strlen($review->comment) > 50)
                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $review->comment }}">
                      {{ substr($review->comment, 0, 50) }}...
                    </span>
                    @else
                    {{ $review->comment }}
                    @endif
                  </span>
                </div>
              </td>
              <td class="text-center">
                <div class="d-flex flex-column align-items-center">
                  <span class="badge bg-light text-dark mb-1">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ $review->created_at->format('d-m-Y') }}
                  </span>
                  <small class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    {{ $review->created_at->format('H:i') }}
                  </small>
                </div>
              </td>
              <td class="text-center">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $review->review_id }}">
                    <i class="bi bi-eye me-1"></i> Xem
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteReviewModal-{{ $review->review_id }}">
                    <i class="bi bi-trash me-1"></i> Xóa
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <div class="d-flex flex-column align-items-center justify-content-center">
                  <i class="bi bi-chat-square-text display-4 mb-3 opacity-50"></i>
                  <h5 class="fw-normal">Chưa có đánh giá nào</h5>
                  <p class="mb-0 small">Hiện tại chưa có khách hàng nào gửi đánh giá.</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
      <small>
        Hiển thị từ <strong>{{ $reviews->firstItem() }}</strong> đến <strong>{{ $reviews->lastItem() }}</strong> trong tổng số <strong>{{ $reviews->total() }}</strong> đánh giá
      </small>
    </div>
    <div>
      {{ $reviews->onEachSide(1)->links('vendor.pagination.custom-pagination') }}
    </div>
  </div>
</div>

{{-- Modal Xem Chi Tiết --}}
@foreach ($reviews as $review)
<div class="modal fade" id="viewModal{{ $review->review_id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $review->review_id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewModalLabel{{ $review->review_id }}">Chi Tiết Đánh Giá</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <div class="text-center mb-3">
              <img src="{{ asset('images/client/avatar/' . $review->user->avatar) }}"
                alt="Avatar"
                class="rounded-circle border border-primary shadow-sm mb-3"
                style="width: 80px; height: 80px; object-fit: cover;">
              <h5 class="mb-0 fw-bold">{{ $review->user->user_name }}</h5>

              <div class="d-flex justify-content-center mt-2">
                @for ($i = 1; $i <= 5; $i++)
                  <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-secondary' }} fs-4 mx-1"></i>
                  @endfor
              </div>
              <span class="badge bg-{{ $review->rating >= 4 ? 'success' : ($review->rating >= 3 ? 'warning' : 'danger') }} mt-2 fs-6 px-3 py-2">
                {{ $review->rating }}/5
              </span>

              <div class="mt-4">
                <p class="mb-1 text-muted small">Đánh giá vào lúc:</p>
                <p class="mb-0 fw-medium">{{ $review->created_at->format('d-m-Y H:i') }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card h-100">
              <div class="card-header bg-light">
                <div class="d-flex align-items-center">
                  <a href="{{ route('admin.tour.detail', ['id' => $review->tour->tour_id ]) }}">
                    <img src="{{ asset('images/tours/' . $review->tour->image_main) }}"
                      alt="Hình ảnh tour"
                      class="rounded shadow-sm border me-3"
                      style="width: 50px; height: 50px; object-fit: cover;">
                  </a>
                  <div>
                    <h6 class="mb-0 fw-bold">{{ $review->tour->tour_name }}</h6>
                    <small class="text-muted">Mã tour: {{ $review->tour->tour_id }}</small>
                  </div>
                </div>
              </div>

              <div class="card-body">
                {{-- 1. Nội dung đánh giá --}}
                <h6 class="fw-bold mb-2">Nội dung đánh giá:</h6>
                <div class="bg-light rounded p-3 mb-4 border">
                  @if($review->comment)
                  <p class="mb-0 text-dark">{{ $review->comment }}</p>
                  @else
                  <p class="mb-0 text-muted fst-italic">
                    <i class="bi bi-chat-square-dots me-1"></i> Không có bình luận chi tiết.
                  </p>
                  @endif
                </div>

                {{-- 2. Hình ảnh đánh giá--}}
                <h6 class="fw-bold mb-2">Hình ảnh đánh giá:</h6>

                {{-- Kiểm tra mảng images có tồn tại và có phần tử không --}}
                @if ($review->images && count($review->images) > 0)
                <div class="d-flex flex-wrap gap-2">
                  @foreach($review->images as $img)
                  <img src="{{ asset('images/reviews/' . $img) }}"
                    alt="Hình ảnh đánh giá"
                    class="img-fluid rounded shadow-sm border"
                    style="height: 150px; width: auto; object-fit: cover;">
                  @endforeach
                </div>
                @else
                {{-- Hiển thị khi KHÔNG có ảnh --}}
                <div class="alert alert-secondary d-flex align-items-center" role="alert">
                  <i class="bi bi-image-alt me-2"></i>
                  <div>Không có hình ảnh đính kèm.</div>
                </div>
                @endif

              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>

        {{-- data-bs-toggle="modal" sẽ tự động đóng modal hiện tại và mở modal mới --}}
        <button type="button" class="btn btn-danger"
          data-bs-toggle="modal"
          data-bs-target="#deleteReviewModal-{{ $review->review_id }}">
          <i class="bi bi-trash me-1"></i> Xóa Đánh Giá
        </button>
      </div>
    </div>
  </div>
</div>

@if ($review->images)
{{-- Modal Hiển thị Hình Ảnh --}}
<div class="modal fade" id="imageModal{{ $review->review_id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $review->review_id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="imageModalLabel{{ $review->review_id }}">Hình Ảnh Đánh Giá</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-0">
        @if($review->images && count($review->images) > 0)
        {{-- Bắt đầu Carousel --}}
        <div id="carouselReview{{ $review->review_id }}" class="carousel slide" data-bs-ride="carousel">
          {{-- Các slide ảnh --}}
          <div class="carousel-inner bg-dark">
            @foreach($review->images as $key => $img)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
              <div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
                <img src="{{ asset('images/reviews/' . $img) }}"
                  class="d-block"
                  alt="Hình ảnh đánh giá {{ $key + 1 }}"
                  style="max-height: 100%; object-fit: contain;">
              </div>
            </div>
            @endforeach
          </div>

          {{-- Nút điều hướng (Chỉ hiện nếu có nhiều hơn 1 ảnh) --}}
          @if(count($review->images) > 1)
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselReview{{ $review->review_id }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselReview{{ $review->review_id }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
          @endif
        </div>

        @else
        <div class="text-center py-5 text-muted">
          <i class="bi bi-image-slash fa-3x mb-3"></i>
          <p>Không có hình ảnh chi tiết.</p>
        </div>
        @endif
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        @if($review->images && count($review->images) > 0)
        {{-- Thêm ID động: downloadBtn + ID bài đánh giá --}}
        <a href="{{ asset('images/reviews/' . $review->images[0]) }}"
          download
          id="downloadBtn{{ $review->review_id }}"
          class="btn btn-primary">
          <i class="bi bi-download me-1"></i> Tải ảnh này
        </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endif
@endforeach

@foreach ($reviews as $review)
{{-- Modal Xác nhận Xóa --}}
<div class="modal fade" id="deleteReviewModal-{{$review->review_id}}" tabindex="-1" aria-labelledby="deleteReviewModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body text-center p-4">
        <div class="mb-4">
          <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
        </div>
        <h4 class="mb-3">Bạn chắc chắn muốn xóa?</h4>
        <p class="text-muted mb-0">Hành động này không thể hoàn tác. Đánh giá sẽ bị xóa vĩnh viễn khỏi hệ thống.</p>
      </div>

      <div class="modal-footer justify-content-center border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-2"></i>Hủy bỏ
        </button>

        <form action="{{ route('admin.review.delete', ['id' => $review->review_id]) }}" method="POST" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger px-4">
            <i class="bi bi-trash me-2"></i>Xác nhận xóa
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endforeach
@endsection

@section('scripts')
<script src="{{ asset('js/admin/reviews/review.js') }}"></script>
@endsection