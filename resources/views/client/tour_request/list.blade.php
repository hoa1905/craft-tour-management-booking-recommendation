@extends('layouts.app')

@section('title', 'Quản lý yêu cầu tour')

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/flatpickrcus.css') }}">
@endsection

@section('content')
<div class="container" style="margin-top: 20px;">
  <div class="row">
    <div class="col-md-12">
      <h3 style="font-size: 18px;"><i class="fa fa-list-alt"></i> Danh sách yêu cầu tour của bạn</h3>
      <hr>

      @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if($requests->count() > 0)
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr class="info">
              <th class="text-center">STT</th>
              <th class="text-center">Ngày gửi</th>
              <th class="text-center">Điểm đến</th>
              <th class="text-center">Ngày đi</th>
              <th class="text-center">Ngày về</th>
              <th class="text-center">Số khách</th>
              <th class="text-center">Trạng thái</th>
              <th class="text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requests as $request)
            <tr>
              <td class="text-center"> {{ $requests->firstItem() + $loop->index }}</td>
              <td class="text-center">{{ $request->created_at->format('d/m/Y H:i') }}</td>
              <td class="text-center">
                <strong>{{ $request->desired_destination }}</strong>
                @if(!empty($request->village_ids))
                <br><small class="text-muted"><i class="fa fa-map-marker"></i> {{ count($request->village_ids) }} làng nghề</small>
                @endif
              </td>
              <td class="text-center">
                {{ $request->start_date->format('d/m/Y') }}
              </td>
              <td class="text-center">
                {{$request->end_date->format('d/m/Y') }}
              </td>
              <td class="text-center">
                {{ $request->adult_quantity }} Lớn
                @if($request->child_quantity > 0), {{ $request->child_quantity }} Trẻ @endif
              </td>
              <td class="text-center">
                <span class="label {{ $request->status_class }}" style="font-size: 90%;">
                  {{ $request->status_label }}
                </span>
              </td>
              <td class="text-center">
                <a href="{{ route('tour_request.detail', ['id' => $request->request_id]) }}" class="btn btn-info btn-xs" title="Xem chi tiết">
                  <i class="fa fa-eye"></i> Xem
                </a>

                {{-- Nút Sửa (Chỉ hiện khi pending) --}}
                @if($request->status == 'pending')
                <a href="{{ route('tour_request.edit', ['id' => $request->request_id]) }}" class="btn btn-warning btn-xs" title="Chỉnh sửa">
                  <i class="fa fa-edit"></i> Sửa
                </a>
                @endif

                {{-- Nút Hủy (Chỉ hiện khi pending) --}}
                @if($request->status == 'pending')
                <form action="{{ route('tour_request.cancel', ['id' => $request->request_id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-xs" title="Hủy yêu cầu">
                    <i class="fa fa-times"></i> Hủy
                  </button>
                </form>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{-- Phân trang --}}
      <div style="display: flex;">
        <div style="display: flex; justify-content: left; align-items: left; width: 100%;">
          <small>
            Hiển thị từ <strong>{{ $requests->firstItem() }}</strong> đến <strong>{{ $requests->lastItem() }}</strong> trong tổng số <strong>{{ $requests->total() }}</strong> tour theo yêu cầu
          </small>
        </div>
        <div style="display: flex; justify-content: right; align-items: right; width: 100%;">
          {{ $requests->onEachSide(1)->links('vendor.pagination.custom-pagination') }}
        </div>
      </div>
      @else
      <div class="alert alert-warning text-center">
        <p>Bạn chưa có yêu cầu tour nào.</p>
        <a href="{{ route('tour_request.create') }}" class="btn btn-warning" style="margin-top: 10px;">Tạo yêu cầu ngay</a>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection