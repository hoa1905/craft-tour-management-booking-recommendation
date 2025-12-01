@extends('layouts.admin')

@section('title', 'Chỉnh sửa tour theo yêu cầu')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
  <form action="{{ route('admin.tour_request.update_content', ['id' => $request->request_id]) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm mb-4">
      <div>
        <h3 class="fw-bold text-primary mb-1">Chỉnh sửa và Xử lý tour theo yêu cầu</h3>
      </div>
      <div>
        <button type="submit" class="btn btn-success px-4 fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Cập nhật</button>
      </div>
    </div>

    <!-- @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
    @endif -->

    <div class="row">
      {{-- Cột trái --}}
      <div class="col-lg-8">
        {{-- Báo giá và Chốt đơn --}}
        <div class="card shadow border-0 rounded-3 mb-4 border-start border-success border-5">
          <div class="card-header bg-success bg-opacity-10 py-3">
            <h5 class="mb-0 fw-bold text-success"><i class="bi bi-cash-coin me-2"></i>Báo giá</h5>
          </div>
          <div class="card-body p-4">
            <div class="alert alert-light border text-muted small mb-3">
              <i class="bi bi-info-circle me-1"></i> Nhập giá chốt cho từng đối tượng. Tổng tiền sẽ tự động tính toán.
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-bold">Giá vé Người lớn</label>
                <div class="input-group">
                  <input type="text" name="price_adult_final" id="price_adult_final"
                    class="form-control currency fw-bold"
                    placeholder="0"
                    value="{{ old('price_adult_final', number_format($request->price_adult_final ?? 0, 0, ',', '.')) }}">
                  <span class="input-group-text">đ</span>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">Giá vé Trẻ em</label>
                <div class="input-group">
                  <input type="text" name="price_child_final" id="price_child_final"
                    class="form-control currency fw-bold"
                    placeholder="0"
                    value="{{ old('price_child_final', number_format($request->price_child_final ?? 0, 0, ',', '.')) }}">
                  <span class="input-group-text">đ</span>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-danger">Tổng tiền</label>
                <div class="input-group">
                  <input type="text" id="final_total_display" class="form-control fw-bold text-danger bg-white" readonly value="0">
                  <span class="input-group-text text-danger fw-bold">đ</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Thông tin chuyến đi --}}
        <div class="card shadow border-0 rounded-3 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Thông tin chuyến đi</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                <input type="text" name="departure_point" class="form-control @error('departure_point') is-invalid @enderror" value="{{ old('departure_point', $request->departure_point) }}">
                @error('departure_point') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Điểm đến (Tự động)</label>
                <input type="text" name="desired_destination" id="desired_destination" class="form-control bg-light" readonly value="{{ old('desired_destination', $request->desired_destination) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Làng nghề tham quan <span class="text-danger">*</span></label>
                <select name="village_ids[]" id="village_select" class="form-select select2-basic @error('village_ids') is-invalid @enderror" multiple>
                  @php $selectedIds = old('village_ids', $request->village_ids ?? []); @endphp
                  @foreach($villages as $v)
                  <option value="{{ $v->village_id }}"
                    data-province="{{ $v->province->province_name }}"
                    {{ in_array($v->village_id, $selectedIds) ? 'selected' : '' }}>
                    {{ $v->village_name }} ({{ $v->province->province_name }})
                  </option>
                  @endforeach
                </select>
                @error('departure_point') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12">
                <label class="form-label text-warning fw-bold">Hoạt động trải nghiệm / Workshop mong muốn</label>
                <textarea name="workshop" class="form-control" rows="2">{{ old('workshop', $request->workshop) }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Ngày đi <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $request->start_date ? $request->start_date->format('Y-m-d') : '') }}">
                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Ngày về <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $request->end_date ? $request->end_date->format('Y-m-d') : '') }}">
                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Người lớn <span class="text-danger">*</span></label>
                <input type="number" id="adult_qty" name="adult_quantity" class="form-control @error('adult_quantity') is-invalid @enderror" min="1" value="{{ old('adult_quantity', $request->adult_quantity) }}">
                @error('adult_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Trẻ em</label>
                <input type="number" id="child_qty" name="child_quantity" class="form-control @error('child_quantity') is-invalid @enderror" min="0" value="{{ old('child_quantity', $request->child_quantity) }}">
                @error('child_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Ngân sách tối thiểu</label>
                <input type="text" name="budget_min" class="form-control currency" value="{{ old('budget_min', number_format($request->budget_min, 0, ',', '.')) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Ngân sách tối đa</label>
                <input type="text" name="budget_max" class="form-control currency" value="{{ old('budget_max', number_format($request->budget_max, 0, ',', '.')) }}">
              </div>
              <div class="col-md-12">
                <label class="form-label">Phương tiện</label>
                <select name="transportation" class="form-select">
                  @foreach(['Bất kỳ', 'Ô tô', 'Xe khách', 'Máy bay', 'Tàu hỏa', 'Xe máy'] as $type)
                  <option value="{{ $type }}" {{ old('transportation', $request->transportation) == $type ? 'selected' : '' }}>{{ $type }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Yêu cầu đặc biệt khác (Ăn uống, khách sạn...)</label>
                <textarea name="requirements" class="form-control" rows="3">{{ old('requirements', $request->requirements) }}</textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Cột phải --}}
      <div class="col-lg-4">
        <div class="card shadow border-0 rounded-3 mb-4">
          <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2"></i>Xử lý yêu cầu</h5>
          </div>
          <div class="card-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Trạng thái</label>
              <select name="status" class="form-select">
                <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="offering" {{ $request->status == 'offering' ? 'selected' : '' }}>Đã gửi báo giá</option>
                <option value="accepted" {{ $request->status == 'accepted' ? 'selected' : '' }}>Đã chốt</option>
                <option value="canceled" {{ $request->status == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Ghi chú</label>
              <textarea name="admin_note" class="form-control" rows="5">{{ old('admin_note', $request->admin_note) }}</textarea>
            </div>
          </div>
        </div>

        <div class="card shadow border-0 rounded-3 mb-4">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Họ tên <span class="text-danger">*</span></label>
              <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $request->full_name) }}">
              @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
              <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $request->phone_number) }}">
              @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}">
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    // 1. Select2
    $('.select2-basic').select2({
      theme: "bootstrap-5",
      width: '100%'
    });

    // 2. Tự động cập nhật điểm đến
    $('#village_select').on('change', function() {
      var selectedProvinces = [];
      $('#village_select option:selected').each(function() {
        var province = $(this).data('province');
        if (province && !selectedProvinces.includes(province)) {
          selectedProvinces.push(province);
        }
      });
      $('#desired_destination').val(selectedProvinces.join(', '));
    });

    // 3. Format tiền tệ
    $('.currency').on('input', function() {
      let value = $(this).val().replace(/[^0-9]/g, '');
      if (value) value = new Intl.NumberFormat('vi-VN').format(value);
      $(this).val(value);
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('.select2-basic').select2({
      theme: "bootstrap-5",
      width: '100%'
    });

    // Format tiền tệ VNĐ
    function formatCurrency(input) {
      let value = input.value.replace(/[^0-9]/g, '');
      input.value = new Intl.NumberFormat('vi-VN').format(value);
    }

    // Tính tổng tiền tự động
    function calculateTotal() {
      let adultQty = parseInt($('#adult_qty').val()) || 0;
      let childQty = parseInt($('#child_qty').val()) || 0;

      let priceAdult = parseInt($('#price_adult_final').val().replace(/\./g, '')) || 0;
      let priceChild = parseInt($('#price_child_final').val().replace(/\./g, '')) || 0;

      let total = (adultQty * priceAdult) + (childQty * priceChild);

      $('#final_total_display').val(new Intl.NumberFormat('vi-VN').format(total));
    }

    // Sự kiện input
    $('.currency').on('input', function() {
      formatCurrency(this);
      calculateTotal();
    });

    $('#adult_qty, #child_qty').on('input', function() {
      calculateTotal();
    });

    // Chạy 1 lần khi load trang
    calculateTotal();
  });
</script>
@endsection