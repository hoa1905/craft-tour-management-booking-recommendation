@extends('layouts.admin')

@section('title', 'Thêm tour mới')

@section('head')
{{-- Thêm CSS cho Select2 (để làm đẹp dropdown) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="{{ asset('css/admin/tours/add.css') }}">
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
  <form action="{{ route('admin.tour.addTour') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- HEADER: Tiêu đề và Nút Submit --}}
    <div class="d-flex justify-content-between align-items-center bg-white p-md-3 rounded-3 shadow-sm mb-4">
      <div>
        <h3><span class="text-primary">Thêm tour mới</h3>
      </div>
      <div>
        <button type="submit" class="btn btn-success btn-lg px-4 d-flex align-items-center justify-content-center">
          <i class="bi bi-plus-circle-fill me-2"></i>
          <span style="font-size: 1.1rem; font-weight: 600;">Thêm tour mới</span>
        </button>
      </div>
    </div>

    {{-- Khối này sẽ hiển thị các lỗi chung (nếu có) --}}
    <!-- @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Có lỗi xảy ra:</strong>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif -->

    <div class="row">
      {{-- Bố cục FULL-WIDTH --}}
      <div class="col-lg-12">
        {{-- Card 1: Thông tin cơ bản --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i> Thông tin cơ bản</h5>
          </div>
          <div class="card-body p-4">
            {{-- Hàng 1: Tên, Phương tiện --}}
            <div class="row">
              <div class="col-md-8 mb-3">
                <label for="tour_name" class="form-label">Tên tour <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('tour_name') is-invalid @enderror"
                  name="tour_name" value="{{ old('tour_name') }}">
                @error('tour_name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4 mb-3">
                <label for="transport" class="form-label">Phương tiện <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('transport') is-invalid @enderror" name="transport" value="{{ old('transport') }}">
                @error('transport')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Hàng 2: Giá, Thời lượng --}}
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="price_adult_display" class="form-label">Giá người lớn <span class="text-danger">*</span></label>
                <input type="text" class="form-control price-format @error('price_adult') is-invalid @enderror" name="price_adult_display"
                  data-hidden="price_adult_hidden"
                  value="{{ old('price_adult_display') }}">
                <input type="hidden" name="price_adult" id="price_adult_hidden" value="{{ old('price_adult') }}">
                @error('price_adult')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4 mb-3">
                <label for="price_child_display" class="form-label">Giá trẻ em <span class="text-danger">*</span></label>
                <input type="text" class="form-control price-format @error('price_child') is-invalid @enderror" name="price_child_display"
                  data-hidden="price_child_hidden"
                  value="{{ old('price_child_display') }}">
                <input type="hidden" name="price_child" id="price_child_hidden" value="{{ old('price_child') }}">
                @error('price_child')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-4 mb-3">
                <label for="duration" class="form-label">Thời lượng tour <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('duration') is-invalid @enderror"
                  name="duration" value="{{ old('duration') }}"
                  placeholder="Ví dụ: 3 ngày 2 đêm">
                @error('duration')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>

        {{-- Card 2: Hình ảnh --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"> <i class="fas fa-images me-2"></i> Hình ảnh</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">

              {{-- Ảnh đại diện --}}
              <div class="col-md-6">
                <div class="card bg-light border-0 shadow-sm h-100">
                  <div class="card-body d-flex flex-column">
                    <label for="image_main" class="form-label fw-semibold mb-2">Ảnh đại diện làng nghề (1 ảnh) <span class="text-danger">*</span></label>

                    <div class="upload-zone p-3 border-2 border-dashed rounded-3 position-relative d-flex flex-column justify-content-center align-items-center bg-white flex-grow-1" style="min-height: 160px;">
                      <div class="text-center upload-instruction">
                        <i class="bi bi-cloud-arrow-up-fill fa-3x text-secondary mb-2 fs-1"></i>
                        <p class="mb-0 text-muted small">Chọn ảnh chính</p>
                      </div>

                      {{-- SỬA: Đã xóa onchange --}}
                      <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                        id="image_main" name="image_main" accept="image/*">

                      <div id="image-preview" class="d-flex flex-wrap w-100 justify-content-center mt-2"></div>
                    </div>
                    @error('image_main') <div class="text-danger mt-2 small">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>

              {{-- Thư viện ảnh --}}
              <div class="col-md-6">
                <div class="card bg-light border-0 shadow-sm h-100">
                  <div class="card-body d-flex flex-column">
                    <label for="images" class="form-label fw-semibold mb-2">Thư viện ảnh (Nhiều ảnh)</label>

                    <div class="upload-zone p-3 border-2 border-dashed rounded-3 position-relative d-flex flex-column justify-content-center align-items-center bg-white flex-grow-1" style="min-height: 160px;">
                      <div class="text-center upload-instruction">
                        <i class="bi bi-images fa-3x text-secondary mb-2 fs-1"></i>
                        <p class="mb-0 text-muted small">Chọn hoặc kéo thả nhiều ảnh</p>
                      </div>

                      {{-- SỬA: Đã xóa onchange --}}
                      <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                        id="images" name="images[]" multiple accept="image/*">

                      <div id="description-image-preview" class="d-flex flex-wrap w-100 justify-content-center mt-2 gap-2"></div>
                    </div>
                    @error('images') <div class="text-danger mt-2 small">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        {{-- Bố cục 2 cột --}}
        <div class="row">
          {{-- Cột bên trái --}}
          <div class="col-lg-8">
            {{-- Card 3: Làng nghề --}}
            <div class="card shadow-sm mb-3">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-houses-fill me-2"></i> Làng nghề cho tour</h5>
              </div>
              <div class="card-body p-4">
                <div class="village-picker-container"
                  data-villages-by-province='@json($villages->groupBy("province_id"))'
                  data-default-province-id="{{ old('destination') }}">

                  {{-- Cột bên trái: Nguồn chọn --}}
                  <div class="village-source">
                    <h5>Thêm làng nghề</h5>
                    <div class="mb-3">
                      <label for="province-source" class="form-label">1. Chọn tỉnh (lọc làng nghề) <span class="text-danger">*</span></label>
                      <select id="province-source" class="form-control select2-basic @error('villages') is-invalid @enderror">
                        <option value="">-- Chọn tỉnh --</option>
                        @foreach($provinces as $p)
                        <option value="{{ $p->province_id }}" {{ old('destination') == $p->province_id ? 'selected' : '' }}>
                          {{ $p->province_name }}
                        </option>
                        @endforeach
                      </select>
                      @error('villages')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="mb-3">
                      <label for="village-source" class="form-label">2. Chọn (các) làng nghề <span class="text-danger">*</span></label>
                      <select id="village-source" class="form-control" multiple style="height: 150px;"></select>
                    </div>
                    <button type="button" id="add-village-btn" class="btn btn-primary w-100">
                      <i class="bi bi-plus-circle"></i> Thêm vào tour
                    </button>
                  </div>

                  {{-- Cột bên phải: Đã chọn --}}
                  <div class="village-selected">
                    <h5>Các làng nghề trong tour</h5>
                    <div id="selected-villages-list" class="village-selected-list">
                      {{-- Chỉ lặp qua 'old()' nếu validation thất bại --}}
                      @if(old('villages'))
                      @foreach(old('villages') as $old_village_id)
                      @php $v = $villages->find($old_village_id); @endphp
                      @if($v)
                      <div class="selected-village-item"
                        data-region="{{ $v->province->region->region_name }}"
                        data-province="{{ $v->province->province_name }}"
                        data-province-id="{{ $v->province->province_id }}">
                        <div class="village-info">
                          <strong>{{ $v->village_name }}</strong>
                          <small>{{ $v->province->province_name }} - {{ $v->province->region->region_name }}</small>
                        </div>
                        <button type="button" class="btn-close remove-village-btn" aria-label="Close"></button>
                        <input type="hidden" name="villages[]" value="{{ $v->village_id }}">
                      </div>
                      @endif
                      @endforeach
                      @endif
                      {{-- Không có $tour->villages để lặp ở đây --}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- Cột bên phải --}}
          <div class="col-lg-4">
            {{-- Card 4: Địa điểm --}}
            <div class="card shadow-sm mb-3">
              <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-geo-alt-fill me-2"></i> Địa điểm và Khu vực</h5>
              </div>
              <div class="card-body p-4">
                <div class="mb-3">
                  <label for="departure_point" class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                  <select name="departure_point" id="departure_point" class="form-control select2-basic @error('departure_point') is-invalid @enderror">
                    <option value="">-- Chọn tỉnh --</option>
                    @foreach($provinces as $p)
                    <option value="{{ $p->province_name }}" {{ old('departure_point') == $p->province_name ? 'selected' : '' }}>
                      {{ $p->province_name }}
                    </option>
                    @endforeach
                  </select>
                  @error('departure_point')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
                <div class="mb-2">
                  <label for="tour-destinations-display" class="form-label">Điểm đến (Tỉnh) - (Tự động cập nhật)</label>
                  <input type="text" id="tour-destinations-display" class="form-control" disabled placeholder="(Tự động cập nhật)">
                </div>
                <div>
                  <label for="tour-regions-display" class="form-label">Miền - (Tự động cập nhật)</label>
                  <input type="text" id="tour-regions-display" class="form-control" disabled
                    placeholder="(Tự động cập nhật)">
                </div>
              </div>
            </div>

          </div>
        </div>

        {{-- Card 5: Hoạt động trải nghiệm --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-hands-helping me-1"></i> Hoạt động trải nghiệm (Workshop) <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body p-4">
            <div class="form-text text-muted">Nêu bật các hoạt động thực hành dành cho khách du lịch.</div>
            <textarea id="workshop" name="workshop" class="form-control @error('workshop') is-invalid @enderror" rows="4">{{ old('workshop') }}</textarea>
            @error('workshop')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Card 6: Mô tả tour --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text-fill me-2"></i> Mô tả tour <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body p-4">
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>

        {{-- Card 7: Lịch khởi hành --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check-fill me-2"></i> Lịch khởi hành <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body p-4">
            <div id="schedules-container" class="mb-2"
              data-counter="{{ count(old('schedules', [])) }}"
              data-has-old="{{ old('schedules') ? 'true' : 'false' }}">

              @if (old('schedules'))
              {{-- Lặp qua dữ liệu CŨ (validation failed) --}}
              @foreach(old('schedules') as $i => $scheduleData)
              <div class="row mb-2 schedule-row align-items-end">
                <input type="hidden" name="schedules[{{ $i }}][schedule_id]" value="{{ $scheduleData['schedule_id'] ?? '' }}">
                <div class="col-md-4">
                  <label class="form-label">Ngày bắt đầu</label>
                  <input type="date" name="schedules[{{ $i }}][start_date]"
                    class="form-control start-date @error('schedules.'.$i.'.start_date') is-invalid @enderror"
                    value="{{ $scheduleData['start_date'] ?? '' }}">
                  @error('schedules.'.$i.'.start_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">Ngày kết thúc</label>
                  <input type="date" name="schedules[{{ $i }}][end_date]"
                    class="form-control end-date @error('schedules.'.$i.'.end_date') is-invalid @enderror"
                    value="{{ $scheduleData['end_date'] ?? '' }}">
                  @error('schedules.'.$i.'.end_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-3">
                  <label class="form-label">Số ghế còn trống</label>
                  <input type="number" name="schedules[{{ $i }}][seats_available]"
                    class="form-control seats @error('schedules.'.$i.'.seats_available') is-invalid @enderror"
                    value="{{ $scheduleData['seats_available'] ?? '' }}">
                  @error('schedules.'.$i.'.seats_available')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-danger btn-sm remove-schedule">Xóa</button>
                </div>
              </div>
              @endforeach
              @endif
              {{-- Không có @else vì không có $tour->schedules --}}
            </div>
            <div class="text-center mt-2">
              <button type="button" id="add-schedule" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Thêm lịch khởi hành
              </button>
            </div>
          </div>
        </div>

        {{-- Card 8: Lịch trình tour --}}
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-ol me-2"></i> Lịch trình tour <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body p-4">
            <div id="itineraries-container" class="mb-2"
              data-counter="{{ count(old('itineraries', [])) }}"
              data-has-old="{{ old('itineraries') ? 'true' : 'false' }}">

              @if (old('itineraries'))
              {{-- Lặp qua dữ liệu CŨ (validation failed) --}}
              @foreach(old('itineraries') as $i => $itineraryData)
              <div class="row mb-3 pt-2 border-top itinerary-row">
                <input type="hidden" name="itineraries[{{ $i }}][itinerary_id]" value="{{ $itineraryData['itinerary_id'] ?? '' }}">
                <div class="col-md-1">
                  <label class="form-label">Ngày</label>
                  <input type="number" name="itineraries[{{ $i }}][day_number]"
                    class="form-control day-number @error('itineraries.'.$i.'.day_number') is-invalid @enderror"
                    value="{{ $itineraryData['day_number'] ?? '' }}">
                  @error('itineraries.'.$i.'.day_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-11">
                  <label class="form-label">Hoạt động</label>
                  <textarea name="itineraries[{{ $i }}][activities]"
                    class="form-control activities mb-1 @error('itineraries.'.$i.'.activities') is-invalid @enderror"
                    placeholder="Hoạt động">{{ $itineraryData['activities'] ?? '' }}</textarea>
                  @error('itineraries.'.$i.'.activities')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  <label class="form-label">Ăn uống</label>
                  <textarea name="itineraries[{{ $i }}][meals]" class="form-control meals mb-1 @error('itineraries.'.$i.'.meals') is-invalid @enderror" placeholder="Bữa ăn">{{ $itineraryData['meals'] ?? '' }}</textarea>
                  @error('itineraries.'.$i.'.meals')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  <label class="form-label">Chỗ ở</label>
                  <textarea name="itineraries[{{ $i }}][accommodation]" class="form-control accommodation @error('itineraries.'.$i.'.accommodation') is-invalid @enderror" placeholder="Chỗ ở">{{ $itineraryData['accommodation'] ?? '' }}</textarea>
                  @error('itineraries.'.$i.'.accommodation')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  <button type="button" class="btn btn-danger btn-sm mt-2 remove-itinerary">Xóa</button>
                </div>
              </div>
              @endforeach
              @endif
              {{-- Không có @else --}}
            </div>
            <div class="text-center mt-2">
              <button type="button" id="add-itinerary" class="btn btn-primary">
                <i class="bi bi-clipboard-plus"></i> Thêm lịch trình
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-success w-50 p-3 d-flex align-items-center justify-content-center mx-auto">
      <i class="bi bi-plus-circle-fill me-2"></i>
      <span style="font-size: 1.3rem;">Thêm tour mới</span>
    </button>

  </form>
</div>
@endsection

@section('scripts')
{{-- Thư viện jQuery đã có trong master.blade.php --}}
<script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/admin/tours/add.js') }}"></script>
@endsection