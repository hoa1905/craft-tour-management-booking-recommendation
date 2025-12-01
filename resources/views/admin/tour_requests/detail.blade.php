@extends('layouts.admin')

@section('title', 'Chi tiết tour theo yêu cầu')

@section('content')
<div class="container-fluid px-4 py-4">
  <style>
    .province-block {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      border-left: 4px solid #0d6efd;
      /* Viền xanh điểm nhấn */
      height: 100%;
    }
  </style>
  {{-- Thông báo --}}
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

  <div class="d-flex flex-wrap justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm mb-4">
    <div>
      <div class="d-flex align-items-center gap-3 mb-1">
        <h3 class="fw-bold text-primary mb-0">Chi tiết tour theo yêu cầu</h3>
        <span class="badge {{ str_replace('label-', 'bg-', $request->status_class) }} fs-6 px-3 py-2">
          {{ $request->status_label }}
        </span>
      </div>
      <div class="text-muted small">
        <i class="bi bi-clock-history"></i> Cập nhật lần cuối: {{ $request->updated_at }}
      </div>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="{{ route('admin.tour_request.edit', ['id' => $request->request_id]) }}" class="btn btn-warning fw-bold shadow-sm text-dark">
        <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa và Xử lý
      </a>
    </div>
  </div>

  <div class="row">
    {{-- Cột trái --}}
    <div class="col-lg-8">
      {{-- Chi tiết Báo giá và Chốt đơn (Chỉ hiện khi đã có giá chốt) --}}
      @if($request->final_total_price > 0)
      <div class="card shadow border-0 rounded-3 mb-4 border-start border-success border-5">
        <div class="card-header bg-success bg-opacity-10 py-3">
          <h5 class="mb-0 fw-bold text-success"><i class="bi bi-cash-coin me-2"></i>Chi tiết báo giá</h5>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-borderless mb-0 align-middle">
              <thead class="border-bottom">
                <tr class="text-muted small text-uppercase">
                  <th>Đối tượng</th>
                  <th class="text-center">Số lượng</th>
                  <th class="text-end">Đơn giá chốt</th>
                  <th class="text-end">Thành tiền</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="fw-bold">Người lớn</td>
                  <td class="text-center">{{ $request->adult_quantity }}</td>
                  <td class="text-end">{{ number_format($request->price_adult_final, 0, ',', '.') }} đ</td>
                  <td class="text-end fw-bold">{{ number_format($request->adult_quantity * $request->price_adult_final, 0, ',', '.') }} đ</td>
                </tr>
                @if($request->child_quantity > 0)
                <tr>
                  <td class="fw-bold">Trẻ em</td>
                  <td class="text-center">{{ $request->child_quantity }}</td>
                  <td class="text-end">{{ number_format($request->price_child_final, 0, ',', '.') }} đ</td>
                  <td class="text-end fw-bold">{{ number_format($request->child_quantity * $request->price_child_final, 0, ',', '.') }} đ</td>
                </tr>
                @endif
                <tr class="border-top">
                  <td colspan="3" class="text-end pt-3">
                    <span class="h6 fw-bold text-danger">TỔNG CỘNG:</span>
                  </td>
                  <td class="text-end pt-3">
                    <span class="h4 fw-bold text-danger">{{ number_format($request->final_total_price, 0, ',', '.') }} đ</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Thông tin chuyến đi</h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="text-muted small"><i class="bi bi-geo-alt text-danger me-1"></i> Điểm khởi hành</label>
              <div class="fw-bold">{{ $request->departure_point }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted small"><i class="bi bi-flag-fill me-1"></i> Điểm đến mong muốn</label>
              <div class="fw-bold">{{ $request->desired_destination }}</div>
            </div>
            <div class="col-md-6">
              {{-- Tính toán số ngày --}}
              @php
              $days = $request->start_date->diffInDays($request->end_date) + 1;
              @endphp
              <label class="text-muted small"> <i class="bi bi-calendar-week me-1"></i> Thời gian: {{ $days }} ngày {{ $days > 1 ? $days - 1 : 0 }} đêm</label>
              <div class="fw-bold">
                {{ $request->start_date->format('d/m/Y') }} - {{ $request->end_date->format('d/m/Y') }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="text-muted small"><i class="bi bi-people-fill me-1"></i> Số lượng khách</label>
              <div class="fw-bold">
                {{ $request->adult_quantity }} Người lớn, {{ $request->child_quantity }} Trẻ em
              </div>
            </div>
            <div class="col-md-6">
              <label class="text-muted small">Phương tiện</label>
              <div class="fw-bold">{{ $request->transportation }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted small">Ngân sách dự kiến</label>
              <div class="fw-bold text-danger">
                {{ number_format($request->budget_min, 0, ',', '.') }} đ - {{ number_format($request->budget_max, 0, ',', '.') }} đ
              </div>
            </div>

            {{-- Danh sách làng nghề theo tỉnh --}}
            <div class="col-12">
              <hr class="my-2 text-muted opacity-25">
              <label class="text-muted small mb-2"><i class="bi bi-camera-fill me-1"></i> Các làng nghề đã chọn tham quan</label>

              {{-- Logic xử lý dữ liệu trực tiếp tại View (hoặc truyền từ Controller) --}}
              @php
              $selectedVillages = collect();
              if (!empty($request->village_ids)) {
              $selectedVillages = \App\Models\Village::with('province')
              ->whereIn('village_id', $request->village_ids)
              ->get();
              }

              // Nhóm theo tên Tỉnh
              $groupedVillages = $selectedVillages->groupBy(function($item) {
              return $item->province->province_name ?? 'Chưa xác định';
              });
              @endphp

              @if($groupedVillages->count() > 0)
              <div class="row g-3">
                @foreach($groupedVillages as $provinceName => $villages)
                <div class="col-md-6 mb-3">
                  <div class="province-block">
                    <div class="d-flex align-items-center mb-2 pb-1 border-bottom border-secondary-subtle">
                      <i class="bi bi-geo-alt-fill text-danger me-2 mt-1"></i>
                      <strong class="text-dark" style="font-size: 0.95rem;">{{ $provinceName }}</strong>
                      <span class="badge bg-primary ms-auto rounded-pill">{{ count($villages) }}</span>
                    </div>

                    {{-- Danh sách Làng nghề thuộc tỉnh đó --}}
                    <ul class="list-unstyled ms-1 mt-1 mb-0 border-start border-2 ps-2 border-primary-subtle">
                      @foreach($villages as $village)
                      <li class="mb-1">
                        <a href="{{ route('admin.village.detail', ['id' => $village->village_id]) }}"
                          target="_blank"
                          class="text-decoration-none text-secondary small hover-primary">
                          <i class="bi bi-dot"></i>
                          {{ $village->village_name }}
                        </a>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
                @endforeach
              </div>
              @endif
            </div>

            <div class="col-12">
              <hr>
              <span class="detail-label text-warning"><i class="bi bi-tools me-1"></i> Hoạt động trải nghiệm / Workshop mong muốn</span>
              <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning border-opacity-25 text-dark">
                {{ $request->workshop ?? 'Không có yêu cầu cụ thể.' }}
              </div>
            </div>
            <div class="col-12">
              <span class="detail-label"><i class="bi bi-chat-left-text me-1"></i> Yêu cầu đặc biệt khác (Ăn uống, khách sạn...)</span>
              <div class="bg-light p-3 rounded border text-secondary">
                {{ $request->requirements ?? 'Không có yêu cầu cụ thể.' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Cột phải --}}
    <div class="col-lg-4">
      {{-- Card: >Ghi chú của Quản trị viên --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-chat-square-quote me-2"></i>Ghi chú của Quản trị viên</h5>
        </div>
        <div class="card-body p-4">
          @if($request->admin_note)
          <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning border-opacity-25 text-dark">
            {{ $request->admin_note }}
          </div>
          @else
          <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning border-opacity-25 text-dark">
            Chưa có ghi chú.
          </div>
          @endif
        </div>
      </div>

      {{-- Card: Khách hàng --}}
      <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-circle me-2"></i>Thông tin liên hệ</h5>
        </div>
        <div class="card-body p-4">
          <div class="row">
            <div class="col-md-12">
              <div class="fw-bold">Họ và tên: {{ $request->full_name }}</div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-6">
              <span>
                <a href="tel:{{ $request->phone_number }}" class="text-decoration-none"><i class="bi bi-telephone me-2"></i>{{ $request->phone_number }}</a>
              </span>
            </div>
            <div class="col-md-6">
              <span>
                <a href="mailto:{{ $request->email }}" class="text-decoration-none"><i class="bi bi-envelope me-2"></i>{{ $request->email }}</a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection