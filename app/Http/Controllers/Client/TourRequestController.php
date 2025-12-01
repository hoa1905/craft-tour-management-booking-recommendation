<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TourRequest;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourRequestController extends Controller
{
    // Hiển thị form đặt tour theo yêu cầu
    public function create()
    {
        // Lấy danh sách làng nghề để khách chọn (dùng cho select multiple)
        $villages = Village::with('province')->get();

        return view('client.tour_request.create', compact('villages'));
    }

    // Xử lý lưu form yêu cầu 
    public function store(Request $request)
    {

        // --- Xử lý dữ liệu trước khi validate bỏ dấu chấm (.) trong số tiền để chuyển thành số nguyên
        // VD: "1.000.000" -> "1000000"
        $budgetMin = $request->budget_min ? str_replace('.', '', $request->budget_min) : null;
        $budgetMax = $request->budget_max ? str_replace('.', '', $request->budget_max) : null;

        // Ghi đè lại dữ liệu vào request để validate
        $request->merge([
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
        ]);

        // 1. Validate
        $rules = [
            'full_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'departure_point' => 'required|string|max:100',
            'desired_destination' => 'required|string|max:100',
            'workshop' => 'nullable|string',
            'village_ids' => 'required|array',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'adult_quantity' => 'required|integer|min:1',
            'child_quantity' => 'nullable|integer|min:0',

            // Validate số bình thường (vì đã xóa dấu chấm ở trên)
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min', // gte: lớn hơn hoặc bằng min
            'transportation' => 'required|in:Ô tô,Xe khách,Tàu hỏa,Máy Bay,Bất kỳ',
            'requirements' => 'nullable|string',
        ];

        $messages = [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'email.required' => 'Vui lòng nhập email.',
            'village_ids.required' => 'Vui lòng chọn làng nghề truyển thống muốn đến',
            'start_date.required' => 'Vui lòng chọn ngày đi.',
            'start_date.after_or_equal' => 'Ngày đi phải từ hôm nay trở đi.',
            'end_date.required' => 'Vui lòng chọn ngày về.',
            'end_date.after_or_equal' => 'Ngày về phải sau hoặc bằng ngày đi.',
            'transportation.required' => 'Vui lòng chọn phương tiện',
            'departure_point.required' => 'Vui lòng nhập điểm khởi hành.',
            'desired_destination.required' => 'Vui lòng chọn ít nhất một làng nghề để xác định điểm đến.',
            'adult_quantityinteger' => 'Phải là số nguyên',
            'budget_min.numeric' => 'Ngân sách phải là số.',
            'budget_max.gte' => 'Ngân sách tối đa phải lớn hơn hoặc bằng tối thiểu.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $validatedData['user_id'] = Auth::guard('client')->id();
        $validatedData['status'] = 'pending';

        // Tạo mới
        TourRequest::create($validatedData);

        return redirect()->route('tour_request.index')
            ->with('success', 'Yêu cầu tour của bạn đã được gửi thành công! Chúng tôi sẽ liên hệ sớm nhất.');
    }

    // Danh sách các yêu cầu tour của khách hàng (Quản lý trạng thái)
    public function index()
    {
        $clientId = Auth::guard('client')->id();
        $requests = TourRequest::where('user_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate(1);

        return view('client.tour_request.list', compact('requests'));
    }

    // Xem chi tiết yêu cầu tour
    public function detail($id)
    {
        $clientId = Auth::guard('client')->id();
        $request = TourRequest::where('request_id', $id)
            ->where('user_id', $clientId)
            ->firstOrFail();

        // Lấy danh sách làng nghề và Eager Load quan hệ 'province' để tránh N+1 Query
        $selectedVillages = collect();
        if (!empty($request->village_ids)) {
            $selectedVillages = Village::with('province') // Quan trọng: Load tỉnh
                ->whereIn('village_id', $request->village_ids)
                ->get();
        }

        return view('client.tour_request.detail', compact('request', 'selectedVillages'));
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $clientId = Auth::guard('client')->id();

        // Tìm yêu cầu của đúng user này
        $request = TourRequest::where('request_id', $id)
            ->where('user_id', $clientId)
            ->firstOrFail();

        // Chặn nếu trạng thái không phải là 'pending' thì không cho sửa
        if ($request->status !== 'pending') {
            return redirect()->route('tour_request.detail', $id)
                ->with('error', 'Yêu cầu này đang được xử lý hoặc đã hoàn tất, bạn không thể chỉnh sửa. Vui lòng liên hệ Admin.');
        }

        // Lấy danh sách làng nghề để hiện select box
        $villages = Village::with('province')->get();

        return view('client.tour_request.edit', compact('request', 'villages'));
    }

    // Xử lý cập nhật
    public function update(Request $request, $id)
    {
        $clientId = Auth::guard('client')->id();
        $tourRequest = TourRequest::where('request_id', $id)
            ->where('user_id', $clientId)
            ->firstOrFail();

        // chặn lần 2 (Bảo mật)
        if ($tourRequest->status !== 'pending') {
            return back()->with('error', 'Không thể chỉnh sửa yêu cầu đã được xử lý.');
        }

        // ---  Xử lý dữ liệu trước khi validate ---
        // Xóa dấu chấm trong chuỗi tiền tệ (VD: "1.000.000" -> "1000000")
        $budgetMin = $request->budget_min ? str_replace('.', '', $request->budget_min) : null;
        $budgetMax = $request->budget_max ? str_replace('.', '', $request->budget_max) : null;

        // Ghi đè dữ liệu đã làm sạch vào Request
        $request->merge([
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
        ]);

        // 1. Validate
        $rules = [
            'full_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'departure_point' => 'required|string|max:100',
            'desired_destination' => 'required|string|max:100',
            'workshop' => 'nullable|string',
            'village_ids' => 'required|array',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'adult_quantity' => 'required|integer|min:1',
            'child_quantity' => 'nullable|integer|min:0',

            // Validate số bình thường (vì đã xóa dấu chấm ở trên)
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min', // gte: lớn hơn hoặc bằng min
            'transportation' => 'required|in:Ô tô,Xe khách,Tàu hỏa,Máy Bay,Bất kỳ',
            'requirements' => 'nullable|string',
        ];

        $messages = [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'email.required' => 'Vui lòng nhập email.',
            'village_ids.required' => 'Vui lòng chọn làng nghề truyển thống muốn đến',
            'start_date.required' => 'Vui lòng chọn ngày đi.',
            'start_date.after_or_equal' => 'Ngày đi phải từ hôm nay trở đi.',
            'end_date.required' => 'Vui lòng chọn ngày về.',
            'end_date.after_or_equal' => 'Ngày về phải sau hoặc bằng ngày đi.',
            'transportation.required' => 'Vui lòng chọn phương tiện',
            'departure_point.required' => 'Vui lòng nhập điểm khởi hành.',
            'desired_destination.required' => 'Vui lòng chọn ít nhất một làng nghề để xác định điểm đến.',
            'adult_quantityinteger' => 'Phải là số nguyên',
            'budget_min.numeric' => 'Ngân sách phải là số.',
            'budget_max.gte' => 'Ngân sách tối đa phải lớn hơn hoặc bằng tối thiểu.',
        ];

        $validatedData = $request->validate($rules, $messages);

        // Cập nhật
        $tourRequest->update($validatedData);

        return redirect()->route('tour_request.detail', $id)
            ->with('success', 'Cập nhật yêu cầu thành công!');
    }

    // Hủy yêu cầu (nếu chưa được xử lý)
    public function cancel($id)
    {
        $clientId = Auth::guard('client')->id();
        $request = TourRequest::where('request_id', $id)
            ->where('user_id', $clientId)
            ->firstOrFail();

        if ($request->status == 'pending') {
            $request->status = 'canceled';
            $request->save();
            return back()->with('success', 'Đã hủy yêu cầu tour.');
        }

        return back()->with('error', 'Không thể hủy yêu cầu này vì đã được xử lý.');
    }

    /**
     * Chuyển dữ liệu từ Request sang Session booking_data để tái sử dụng giao diện booking
     */
    public function checkout($id)
    {
        $clientId = Auth::guard('client')->id();
        $request = TourRequest::where('request_id', $id)
            ->where('user_id', $clientId)
            ->firstOrFail();

        if ($request->status !== 'accepted' || !$request->final_total_price) {
            return redirect()->route('tour_request.show', $id)
                ->with('error', 'Yêu cầu chưa sẵn sàng để thanh toán.');
        }

        // --- Xử lý lấy tên làng nghề ---
        $villageNames = '';
        if (!empty($request->village_ids)) {
            // Lấy danh sách tên làng nghề dựa trên ID, nối lại bằng dấu phẩy
            $villageNames = \App\Models\Village::whereIn('village_id', $request->village_ids)
                ->pluck('village_name')
                ->implode(', ');
        }

        // Tạo tên tour đầy đủ
        $fullTourName = 'Tour theo yêu cầu: ' . $request->desired_destination;
        if (!empty($villageNames)) {
            $fullTourName .= ' - Tham quan: ' . $villageNames;
        }

        // Chuẩn bị dữ liệu
        $bookingData = [
            'tour_id'                => null,
            'schedule_id'            => null,
            'request_id'             => $request->request_id,
            'tour_name'              => $fullTourName,
            'tour_image'             => 'custom-tour.jpg',
            'start_date'             => $request->start_date->format('d/m/Y'),
            'end_date'               => $request->end_date->format('d/m/Y'),
            'adult_quantity'         => $request->adult_quantity,
            'child_quantity'         => $request->child_quantity ?? 0,
            'price_adult_at_booking' => $request->price_adult_final,
            'price_child_at_booking' => $request->price_child_final ?? 0,
            'total_price'            => $request->final_total_price,
        ];

        // Lưu vào session (giống hệt cách BookingController làm)
        session(['booking_data' => $bookingData]);

        // Chuyển hướng sang trang Booking chung
        return redirect()->route('booking');
    }
}
