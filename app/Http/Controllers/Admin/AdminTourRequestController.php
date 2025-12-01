<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourRequest;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTourRequestController extends Controller
{
    /**
     * Danh sách yêu cầu tour
     */
    public function index(Request $request)
    {
        $query = TourRequest::with('user')->orderBy('created_at', 'desc');

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Phân trang
        $requests = $query->paginate(10);

        return view('admin.tour_requests.list', compact('requests'));
    }

    /**
     * Xem chi tiết và form xử lý
     */
    public function detail($id)
    {
        $request = TourRequest::findOrFail($id);
        return view('admin.tour_requests.detail', compact('request'));
    }

    /**
     * Cập nhật trạng thái và ghi chú của Admin
     */
    public function update(Request $request, $id)
    {
        $tourRequest = TourRequest::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,offering,accepted,rejected,canceled,completed',
            'admin_note' => 'nullable|string'
        ]);

        try {
            $tourRequest->status = $request->status;
            $tourRequest->admin_note = $request->admin_note;
            $tourRequest->save();

            return redirect()->back()->with('status', 'Cập nhật yêu cầu thành công!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra.']);
        }
    }

    /**
     * Hiển thị form chỉnh sửa (Admin)
     */
    public function edit($id)
    {
        $request = TourRequest::findOrFail($id);

        // Lấy dữ liệu để đổ vào select box
        $villages = Village::with('province')->get();

        return view('admin.tour_requests.edit', compact('request', 'villages'));
    }

    /**
     * Xử lý cập nhật nội dung tour request
     */
    public function updateContent(Request $request, $id)
    {
        $tourRequest = TourRequest::findOrFail($id);

        // ---  Xử lý dữ liệu trước khi validate ---
        // Xóa dấu chấm trong chuỗi tiền tệ (VD: "1.000.000" -> "1000000")
        $budgetMin = $request->budget_min ? str_replace('.', '', $request->budget_min) : null;
        $budgetMax = $request->budget_max ? str_replace('.', '', $request->budget_max) : null;

        // Ghi đè dữ liệu đã làm sạch vào Request
        $request->merge([
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
        ]);

        // 1. Validate (Giống bên Client, nhưng Admin có quyền sửa mọi thứ)
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'departure_point' => 'required|string|max:100',
            'desired_destination' => 'required|string|max:100',
            'workshop' => 'nullable|string',
            'village_ids' => 'nullable|array', // Admin có thể xóa hết làng nghề nếu muốn
            'start_date' => 'required|date',   // Admin được phép chọn ngày quá khứ nếu cần fix data
            'end_date' => 'required|date|after_or_equal:start_date',
            'adult_quantity' => 'required|integer|min:1',
            'child_quantity' => 'nullable|integer|min:0',
            'price_adult_final' => 'nullable|string', // Nhận string vì có dấu chấm
            'price_child_final' => 'nullable|string',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'transportation' => 'required|string',
            'requirements' => 'nullable|string',
            // Admin được sửa cả status và note ở đây luôn
            'status' => 'required|in:pending,offering,accepted,rejected,canceled,completed',
            'admin_note' => 'nullable|string'
        ], [
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
            'adult_quantity.integer' => 'Phải là số nguyên',
            'child_quantity.integer' => 'Phải là số nguyên',
            'budget_min.numeric' => 'Ngân sách phải là số.',
            'budget_max.gte' => 'Ngân sách tối đa phải lớn hơn hoặc bằng tối thiểu.',
        ]);

        // 2. Xử lý tiền tệ (Xóa dấu chấm)
        // ... xử lý budget_min/max cũ ...

        // Xử lý giá chốt
        $priceAdult = 0;
        $priceChild = 0;

        if ($request->price_adult_final) {
            $priceAdult = (float) str_replace('.', '', $request->price_adult_final);
            $validatedData['price_adult_final'] = $priceAdult;
        }

        if ($request->price_child_final) {
            $priceChild = (float) str_replace('.', '', $request->price_child_final);
            $validatedData['price_child_final'] = $priceChild;
        }

        // 3. Tự động tính tổng tiền chốt (Backend Calculation để an toàn)
        $adultQty = $request->adult_quantity;
        $childQty = $request->child_quantity ?? 0;

        if ($priceAdult > 0) {
            $validatedData['final_total_price'] = ($priceAdult * $adultQty) + ($priceChild * $childQty);
        } else {
            $validatedData['final_total_price'] = null; // Nếu chưa nhập giá thì chưa có tổng
        }

        // Cập nhật
        $tourRequest->update($validatedData);

        return redirect()->route('admin.tour_request.detail', $id)
            ->with('status', 'Đã cập nhật thông tin yêu cầu thành công!');
    }
}
