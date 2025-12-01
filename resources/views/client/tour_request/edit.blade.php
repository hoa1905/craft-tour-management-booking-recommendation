@extends('layouts.app')

@section('title', 'Chỉnh sửa yêu cầu tour')

@section('content')
<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">

      <div class="panel panel-warning">
        <div class="panel-heading">
          <h3 class="panel-title" style="font-size: 18px; font-weight: bold;">
            <i class="fa fa-pencil"></i> CHỈNH SỬA YÊU CẦU TOUR
          </h3>
        </div>
        <div class="panel-body">
          <form action="{{ route('tour_request.update', $request->request_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Thông tin liên hệ --}}
            <h4 class="page-header" style="color: #f0ad4e; margin-top: 0; font-size: 17px">
              <i class="fa fa-user"></i> 1. Thông tin liên hệ
            </h4>
            <div class="row">
              <div class="col-md-6 form-group {{ $errors->has('full_name') ? 'has-error' : '' }}">
                <label>Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control"
                  value="{{ old('full_name', $request->full_name) }}" required>
                @if ($errors->has('full_name'))
                <span class="help-block"><strong>{{ $errors->first('full_name') }}</strong></span>
                @endif
              </div>
              <div class="col-md-6 form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                <label>Số điện thoại <span class="text-danger">*</span></label>
                <input type="text" name="phone_number" class="form-control"
                  value="{{ old('phone_number', $request->phone_number) }}" required>
                @if ($errors->has('phone_number'))
                <span class="help-block"><strong>{{ $errors->first('phone_number') }}</strong></span>
                @endif
              </div>
              <div class="col-md-12 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control"
                  value="{{ old('email', $request->email) }}" required>
                @if ($errors->has('email'))
                <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                @endif
              </div>
            </div>

            {{-- Thông tin chuyến đi --}}
            <h4 class="page-header" style="color: #f0ad4e; margin-top: 20px; font-size: 17px">
              <i class=" fa fa-map-marker"></i> 2. Thông tin chuyến đi
            </h4>

            <div class="row">
              <div class="col-md-6 form-group">
                <label>Điểm khởi hành <span class="text-danger">*</span></label>
                <input type="text" name="departure_point" class="form-control {{ $errors->has('departure_point') ? 'has-error' : '' }}" placeholder=" VD: TP. Hà Nội, Quảng Nam..."
                  value="{{ old('departure_point', $request->departure_point) }}">
                @if ($errors->has('departure_point'))
                <span class="help-block"><strong>{{ $errors->first('departure_point') }}</strong></span>
                @endif
              </div>
              <div class="col-md-6 form-group {{ $errors->has('desired_destination') ? 'has-error' : '' }}">
                <label>Điểm đến (Tự động cập nhật theo làng nghề)</label>
                <input type="text" name="desired_destination" id="desired_destination" class="form-control"
                  value="{{ old('desired_destination', $request->desired_destination) }}" readonly>
              </div>
              @if ($errors->has('desired_destination'))
              <span class="help-block"><strong>{{ $errors->first('desired_destination') }}</strong></span>
              @endif
            </div>

            <div class="form-group {{ $errors->has('village_ids') ? 'has-error' : '' }}">
              <label>Làng nghề muốn tham quan <span class="text-danger">*</span></label>
              @php
              $selectedIds = old('village_ids', $request->village_ids ?? []);
              @endphp
              <select name="village_ids[]" id="village_select" class="form-control chosen-select" multiple data-placeholder="Chọn các làng nghề...">
                @foreach($villages as $v)
                <option value="{{ $v->village_id }}"
                  data-province="{{ $v->province->province_name }}"
                  {{ in_array($v->village_id, $selectedIds) ? 'selected' : '' }}>
                  {{ $v->village_name }} ({{ $v->province->province_name }})
                </option>
                @endforeach
              </select>
              @if ($errors->has('village_ids'))
              <span class="help-block"><strong>{{ $errors->first('village_ids') }}</strong></span>
              @endif
            </div>

            <div class="form-group {{ $errors->has('workshop') ? 'has-error' : '' }}">
              <label style="color: #d35400;"><i class="fa fa-cogs"></i> Hoạt động trải nghiệm / Workshop mong muốn</label>
              <p class="text-muted" style="font-size: 14px; margin-bottom: 0;">Mô tả các hoạt động thực tế bạn muốn tham gia.</p>
              <textarea name="workshop" class="form-control" rows="2">{{ old('workshop', $request->workshop) }}</textarea>
              @if ($errors->has('workshop'))
              <span class="help-block"><strong>{{ $errors->first('workshop') }}</strong></span>
              @endif
            </div>

            <div class="row">
              <div class="col-md-6 form-group {{ $errors->has('start_date') ? 'has-error' : '' }}">
                <label>Ngày đi dự kiến <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control"
                  value="{{ old('start_date', $request->start_date->format('Y-m-d')) }}">
                @if ($errors->has('start_date'))
                <span class="help-block"><strong>{{ $errors->first('start_date') }}</strong></span>
                @endif
              </div>
              <div class="col-md-6 form-group {{ $errors->has('end_date') ? 'has-error' : '' }}">
                <label>Ngày về dự kiến <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control"
                  value="{{ old('end_date', $request->end_date->format('Y-m-d')) }}">
                @if ($errors->has('end_date'))
                <span class="help-block"><strong>{{ $errors->first('end_date') }}</strong></span>
                @endif
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group {{ $errors->has('adult_quantity') ? 'has-error' : '' }}">
                <label>Số người lớn <span class="text-danger">*</span></label>
                <input type="number" name="adult_quantity" class="form-control" min="1"
                  value="{{ old('adult_quantity', $request->adult_quantity) }}">
                @if ($errors->has('adult_quantity'))
                <span class="help-block"><strong>{{ $errors->first('adult_quantity') }}</strong></span>
                @endif
              </div>
              <div class="col-md-6 form-group {{ $errors->has('child_quantity') ? 'has-error' : '' }}">
                <label>Số trẻ em</label>
                <input type="number" name="child_quantity" class="form-control" min="0"
                  value="{{ old('child_quantity', $request->child_quantity) }}">
                @if ($errors->has('child_quantity'))
                <span class="help-block"><strong>{{ $errors->first('child_quantity') }}</strong></span>
                @endif
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 form-group">
                <label>Ngân sách dự kiến (VNĐ/người)</label>
                <div class="input-group">
                  <input type="text" name="budget_min" class="form-control currency" placeholder="Tối thiểu"
                    value="{{ old('budget_min', number_format($request->budget_min, 0, ',', '.')) }}">
                  <span class="input-group-addon">-</span>
                  <input type="text" name="budget_max" class="form-control currency" placeholder="Tối đa"
                    value="{{ old('budget_max', number_format($request->budget_max, 0, ',', '.')) }}">
                </div>
                @if ($errors->has('budget_min') || $errors->has('budget_max'))
                <span class="help-block text-danger"><strong>Vui lòng kiểm tra lại ngân sách (nhập số).</strong></span>
                @endif
              </div>
              <div class="col-md-6 form-group {{ $errors->has('transportation') ? 'has-error' : '' }}">
                <label>Phương tiện</label>
                <select name="transportation" class="form-control">
                  @foreach(['Bất kỳ', 'Ô tô', 'Xe khách', 'Máy bay', 'Tàu hỏa'] as $type)
                  <option value="{{ $type }}" {{ old('transportation', $request->transportation) == $type ? 'selected' : '' }}>
                    {{ $type }}
                  </option>
                  @endforeach
                </select>
                @if ($errors->has('transportation'))
                <span class="help-block"><strong>{{ $errors->first('transportation') }}</strong></span>
                @endif
              </div>
            </div>

            <div class="form-group {{ $errors->has('requirements') ? 'has-error' : '' }}">
              <label>Yêu cầu đặc biệt khác (Ăn uống, khách sạn...)</label>
              <textarea name="requirements" class="form-control" rows="3">{{ old('requirements', $request->requirements) }}</textarea>
              @if ($errors->has('requirements'))
              <span class="help-block"><strong>{{ $errors->first('requirements') }}</strong></span>
              @endif
            </div>

            <div class="text-center" style="margin-top: 20px;">
              <button type="submit" class="btn btn-warning btn-lg">
                <i class="fa fa-save"></i> Cập nhật yêu cầu
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
{{-- Tái sử dụng file JS của trang create vì logic giống hệt --}}
<script src="{{ asset('js/client/tour_request/create.js') }}"></script>
@endsection