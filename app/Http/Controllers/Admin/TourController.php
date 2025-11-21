<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Tour;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class TourController extends Controller
{
    /**
     * Hiển thị danh sách tour
     * - Tên tour
     * - Giá người lớn
     * - Tên làng nghề
     * - Tỉnh
     * - Miền (Region)
     */
    public function index()
    {
        $tours = Tour::with([
            'villages.province.region'  // load đầy đủ: village → province → region
        ])->paginate(10);

        return view('admin.tours.list', compact('tours'));
    }

    /**
     * Hiển thị chi tiết 1 tour
     * - Tất cả thông tin tour
     * - Lịch khởi hành
     * - Làng nghề liên quan
     * - Lịch trình (itineraries)
     * - Đối tác tạo tour
     * - Các lượt đặt booking liên quan
     */
    public function tourDetail($id)
    {
        $tour = Tour::with([
            'partner',
            'villages.province.region',
            'schedules',
            'itineraries',
            'bookings',
        ])->where('tour_id', $id)->firstOrFail();

        return view('admin.tours.detail', compact('tour'));
    }

    public function showEditForm($id)
    {
        $tour = Tour::with([
            'villages.province.region',
            'schedules',
            'itineraries',
        ])->findOrFail($id);

        // Lấy tất cả tỉnh
        $provinces = Province::all();

        // Lấy tất cả làng nghề kèm province & region
        $villages = Village::with('province.region')->get();

        return view('admin.tours.edit', compact('tour', 'provinces', 'villages'));
    }

    /**
     * Cập nhật thông tin tour trong database.
     */
    public function updateTour(Request $request, $id)
    {
        // 1. Định nghĩa quy tắc (Rules)
        $rules = [
            'tour_name' => 'required|string|max:255',
            'transport' => 'required|string|max:100',
            'price_adult' => 'required|numeric|min:0',
            'price_child' => 'required|numeric|min:0',
            'departure_point' => 'required|string',
            'duration' => 'required|string|max:100',
            'description' => 'required|string',

            // Hình ảnh
            'image_main' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

            // Mảng Làng nghề phải chọn ít nhất 1 làng nghề
            'villages' => 'required|array|min:1',
            'villages.*' => 'exists:villages,village_id', // Đảm bảo mọi ID đều tồn tại

            // Mảng Lịch khởi hành
            'schedules' => 'nullable|array',
            'schedules.*.start_date' => 'required|date',
            'schedules.*.end_date' => 'required|date|after_or_equal:schedules.*.start_date',
            'schedules.*.seats_available' => 'required|integer|min:0',

            // Mảng Lịch trình
            'itineraries' => 'nullable|array',
            'itineraries.*.day_number' => 'required|integer|min:1',
            'itineraries.*.activities' => 'required|string',
            'itineraries.*.meals' => 'required|string',
            'itineraries.*.accommodation' => 'required|string',
        ];

        // 2. Định nghĩa thông báo lỗi tùy chỉnh (Messages)
        // -----------------------------------------------------
        $messages = [
            'tour_name.required' => 'Vui lòng nhập tên tour.',
            'tour_name.max' => 'Tên tour không được vượt quá 255 ký tự.',
            'transport.required' => 'Vui lòng nhập tên phương tiện.',
            'transport.max' => 'Tên phương tiện không được vượt quá 100 ký tự.',
            'price_adult.required' => 'Vui lòng nhập giá người lớn.',
            'price_adult.numeric' => 'Giá người lớn phải là một con số.',
            'price_adult.min' => 'Giá người lớn không được âm.',
            'price_child.required' => 'Vui lòng nhập giá trẻ em.',
            'price_child.numeric' => 'Giá trẻ em phải là một con số.',
            'price_child.min' => 'Giá trẻ em không được âm.',
            'departure_point.required' => 'Vui lòng chọn điểm khởi hành.',
            'duration.required' => 'Vui lòng nhập thời lượng tour.',
            'duration.max' => 'Thời lượng tour không được vượt quá 100 ký tự.',
            'description.required' => 'Vui lòng nhập mô tả tour.',

            // Hình ảnh
            'image_main.image' => 'Ảnh chính phải là tệp hình ảnh.',
            'image_main.mimes' => 'Ảnh chính phải có định dạng: jpeg, png, jpg, gif.',
            'image_main.max' => 'Ảnh chính vượt quá dung lượng cho phép (5MB).',

            'image_1.image' => 'Ảnh 1 phải là tệp hình ảnh.',
            'image_1.mimes' => 'Ảnh 1 phải có định dạng: jpeg, png, jpg, gif.',
            'image_1.max' => 'Ảnh 1 vượt quá dung lượng cho phép (5MB).',

            'image_2.image' => 'Ảnh 2 phải là tệp hình ảnh.',
            'image_2.mimes' => 'Ảnh 2 phải có định dạng: jpeg, png, jpg, gif.',
            'image_2.max' => 'Ảnh 2 vượt quá dung lượng cho phép (5MB).',

            // Làng nghề (dùng * để đại diện cho mọi phần tử trong mảng)
            'villages.required' => 'Bạn phải chọn ít nhất một làng nghề cho tour.',
            'villages.min' => 'Bạn phải chọn ít nhất một làng nghề cho tour.',
            'villages.*.exists' => 'Một làng nghề được chọn không hợp lệ.',

            // Lịch khởi hành
            'schedules.*.start_date.required' => 'Ngày bắt đầu không được để trống.',
            'schedules.*.start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'schedules.*.end_date.required' => 'Ngày kết thúc không được để trống.',
            'schedules.*.end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'schedules.*.seats_available.required' => 'Số ghế không được để trống.',
            'schedules.*.seats_available.integer' => 'Số ghế phải là số nguyên.',
            'schedules.*.seats_available.min' => 'Số ghế không được âm.',

            // Lịch trình
            'itineraries.*.day_number.required' => 'Số ngày không được để trống.',
            'itineraries.*.day_number.integer' => 'Số ngày phải là số nguyên.',
            'itineraries.*.activities.required' => 'Hoạt động không được để trống.',
            'itineraries.*.meals.required' => 'Ăn uống không được để trống.',
            'itineraries.*.accommodation.required' => 'Chỗ ở không được để trống.'
        ];

        // 3. Chạy Validate
        // -----------------------------------------------------
        $validatedData = $request->validate($rules, $messages);

        // 4. Tìm Tour và bắt đầu Transaction
        $tour = Tour::findOrFail($id);

        // Dùng transaction để đảm bảo an toàn,
        // nếu 1 bước lỗi, tất cả sẽ được hoàn tác
        DB::beginTransaction();
        try {

            // 5. Tính toán 'destination' (Tên tỉnh) từ mảng 'villages'
            $destination_name = null;
            if (!empty($validatedData['villages'])) {
                // Lấy các Tên Tỉnh (duy nhất) từ các village_id đã chọn
                $province_names = Village::whereIn('village_id', $validatedData['villages'])
                    ->with('province') // Load quan hệ province
                    ->get()
                    ->pluck('province.province_name') // Lấy tên tỉnh
                    ->unique() // Lọc duy nhất
                    ->implode(', '); // Nối chuỗi (VD: "Hà Nội, Hưng Yên")

                $destination_name = $province_names;
            }

            // 6. Xử lý dữ liệu Tour
            $updateData = [
                'tour_name' => $validatedData['tour_name'],
                'transport' => $validatedData['transport'],
                'price_adult' => $validatedData['price_adult'],
                'price_child' => $validatedData['price_child'],
                'departure_point' => $validatedData['departure_point'],
                'destination' => $destination_name, // Lưu chuỗi tên tỉnh đã tính toán
                'duration' => $validatedData['duration'],
                'description' => $validatedData['description'],
            ];

            // 7. Xử lý Upload Hình ảnh
            $imagePath = public_path('images/client');
            foreach (['image_main', 'image_1', 'image_2'] as $imageField) {
                if ($request->hasFile($imageField)) {

                    // Xóa ảnh cũ (nếu có)
                    if ($tour->{$imageField} && File::exists($imagePath . '/' . $tour->{$imageField})) {
                        File::delete($imagePath . '/' . $tour->{$imageField});
                    }

                    // Upload ảnh mới
                    $file = $request->file($imageField);
                    $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                    $file->move($imagePath, $filename);

                    // Thêm tên file mới vào data để cập nhật
                    $updateData[$imageField] = $filename;
                }
            }

            // Cập nhật các trường cơ bản của Tour
            $tour->update($updateData);

            // 8. Đồng bộ hóa Làng nghề (Many-to-Many)
            // -----------------------------------------------------
            // sync() sẽ tự động xóa các làng nghề không có trong $request
            // và thêm các làng nghề mới.
            $tour->villages()->sync($request->input('villages', []));

            // 9. Đồng bộ hóa Lịch khởi hành (One-to-Many)
            // -----------------------------------------------------
            // Cách an toàn nhất: Xóa hết cái cũ, tạo lại từ request
            $tour->schedules()->delete();
            if ($request->has('schedules')) {
                foreach ($validatedData['schedules'] as $scheduleData) {
                    $tour->schedules()->create($scheduleData);
                }
            }

            // 10. Đồng bộ hóa Lịch trình (One-to-Many)
            // -----------------------------------------------------
            // Tương tự, xóa hết và tạo lại
            $tour->itineraries()->delete();
            if ($request->has('itineraries')) {
                foreach ($validatedData['itineraries'] as $itineraryData) {
                    $tour->itineraries()->create($itineraryData);
                }
            }

            // 11. Hoàn tất
            // -----------------------------------------------------
            DB::commit(); // Mọi thứ thành công, lưu vào DB

            // Chuyển hướng về trang 'detail' với thông báo
            return redirect()->route('admin.tour.detail', ['id' => $id])->with('status', 'Cập nhật tour thành công!');
        } catch (\Exception $e) {
            // Có lỗi xảy ra, hoàn tác mọi thay đổi
            DB::rollBack();

            // Ghi log lỗi (quan trọng cho debug)
            Log::error('Lỗi cập nhật tour: ' . $e->getMessage());

            // Quay lại với thông báo lỗi
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi, không thể cập nhật tour. Vui lòng thử lại.']);
        }
    }

    /**
     * Hiển thị form thêm tour mới.
     */
    public function showAddTourForm()
    {
        // Cần gửi $provinces và $villages rỗng để JS-picker hoạt động
        $provinces = Province::all();
        $villages = Village::with('province.region')->get();

        // Trả về view 'add', không cần $tour
        return view('admin.tours.add', compact('provinces', 'villages'));
    }

    /**
     * Xử lý thêm mới Tour vào database
     */
    public function addTour(Request $request)
    {
        // 1. Xác thực dữ liệu (validation)
        $rules = [
            'tour_name' => 'required|string|max:255',
            'transport' => 'required|string|max:100',
            'price_adult' => 'required|numeric|min:0',
            'price_child' => 'required|numeric|min:0',
            'departure_point' => 'required|string',
            'duration' => 'required|string|max:100',
            'description' => 'required|string',

            // Ảnh: bắt buộc
            'image_main' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            'image_1' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_2' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',

            // Làng nghề: Bắt buộc chọn ít nhất 1
            'villages' => 'required|array|min:1',
            'villages.*' => 'exists:villages,village_id',

            // Lịch khởi hành: Có thể để trống mảng, nhưng nếu nhập phải đúng định dạng
            'schedules' => 'nullable|array',
            'schedules.*.start_date' => 'required|date',
            'schedules.*.end_date' => 'required|date|after_or_equal:schedules.*.start_date',
            'schedules.*.seats_available' => 'required|integer|min:1',

            // Lịch trình chi tiết
            'itineraries' => 'nullable|array',
            'itineraries.*.day_number' => 'required|integer|min:1',
            'itineraries.*.activities' => 'required|string',
            'itineraries.*.meals' => 'required|string',
            'itineraries.*.accommodation' => 'required|string',
        ];

        $messages = [
            'tour_name.required' => 'Vui lòng nhập tên tour.',
            'transport.required' => 'Vui lòng nhập phương tiện di chuyển.',
            'price_adult.required' => 'Vui lòng nhập giá vé người lớn.',
            'price_child.required' => 'Vui lòng nhập giá vé trẻ em.',
            'departure_point.required' => 'Vui lòng chọn điểm khởi hành.',
            'duration.required' => 'Vui lòng nhập thời lượng tour.',
            'description.required' => 'Vui lòng nhập mô tả tour.',
            'image_main.required' => 'Vui lòng chọn ảnh đại diện cho tour.',
            'image_main.max' => 'Ảnh không được vượt quá 5MB.',

            // Hình ảnh
            'image_main.required' => 'Vui lòng chọn ảnh.',
            'image_main.image' => 'Ảnh chính phải là tệp hình ảnh.',
            'image_main.mimes' => 'Ảnh chính phải có định dạng: jpeg, png, jpg, gif.',
            'image_main.max' => 'Ảnh chính vượt quá dung lượng cho phép (5MB).',

            'image_1.required' => 'Vui lòng chọn ảnh.',
            'image_1.image' => 'Ảnh 1 phải là tệp hình ảnh.',
            'image_1.mimes' => 'Ảnh 1 phải có định dạng: jpeg, png, jpg, gif.',
            'image_1.max' => 'Ảnh 1 vượt quá dung lượng cho phép (5MB).',

            'image_2.required' => 'Vui lòng chọn ảnh.',
            'image_2.image' => 'Ảnh 2 phải là tệp hình ảnh.',
            'image_2.mimes' => 'Ảnh 2 phải có định dạng: jpeg, png, jpg, gif.',
            'image_2.max' => 'Ảnh 2 vượt quá dung lượng cho phép (5MB).',

            // Làng nghề (dùng * để đại diện cho mọi phần tử trong mảng)
            'villages.required' => 'Bạn phải chọn ít nhất một làng nghề cho tour.',
            'villages.min' => 'Bạn phải chọn ít nhất một làng nghề cho tour.',
            'villages.*.exists' => 'Một làng nghề được chọn không hợp lệ.',

            // Lịch khởi hành
            'schedules.*.start_date.required' => 'Ngày bắt đầu không được để trống.',
            'schedules.*.start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'schedules.*.end_date.required' => 'Ngày kết thúc không được để trống.',
            'schedules.*.end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'schedules.*.seats_available.required' => 'Số ghế không được để trống.',
            'schedules.*.seats_available.integer' => 'Số ghế phải là số nguyên.',
            'schedules.*.seats_available.min' => 'Số ghế không được âm.',

            // Lịch trình
            'itineraries.*.day_number.required' => 'Số ngày không được để trống.',
            'itineraries.*.day_number.integer' => 'Số ngày phải là số nguyên.',
            'itineraries.*.activities.required' => 'Hoạt động không được để trống.',
            'itineraries.*.meals.required' => 'Ăn uống không được để trống.',
            'itineraries.*.accommodation.required' => 'Chỗ ở không được để trống.'
        ];

        // Chạy validate
        $validatedData = $request->validate($rules, $messages);

        // 2. XỬ LÝ LOGIC & LƯU DATABASE (TRANSACTION)
        DB::beginTransaction(); // Bắt đầu giao dịch
        try {

            // --- BƯỚC A: Tính toán "Điểm đến" (Destination) ---
            // Lấy danh sách tên các Tỉnh từ các Làng nghề đã chọn
            $destination_string = "Chưa cập nhật";
            if (!empty($validatedData['villages'])) {
                $provinces = Village::whereIn('village_id', $validatedData['villages'])
                    ->with('province')
                    ->get()
                    ->pluck('province.province_name')
                    ->unique()
                    ->filter() // Loại bỏ giá trị rỗng
                    ->implode(', ');

                if (!empty($provinces)) {
                    $destination_string = $provinces;
                }
            }

            // Lấy thông tin admin hiện tại
            $user = Auth::guard('admin')->user();

            // --- BƯỚC B: Chuẩn bị dữ liệu Tour ---
            $tourData = [
                'tour_name' => $validatedData['tour_name'],
                'transport' => $validatedData['transport'],
                'price_adult' => $validatedData['price_adult'],
                'price_child' => $validatedData['price_child'],
                'departure_point' => $validatedData['departure_point'],
                'destination' => $destination_string,
                'duration' => $validatedData['duration'],
                'description' => $validatedData['description'],
                'status' => 'approved',
                // Lấy ID người đang đăng nhập (Admin hoặc Partner)
                'partner_id' => $user->user_id,
            ];

            // --- BƯỚC C: Upload Hình ảnh ---
            $imagePath = public_path('images/client');
            // Đảm bảo thư mục tồn tại
            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            foreach (['image_main', 'image_1', 'image_2'] as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    // Đặt tên file: {timestamp}-{random}-ten-file.jpg để tránh trùng
                    $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalExtension();
                    $file->move($imagePath, $filename);
                    $tourData[$field] = $filename;
                }
            }
            // --- Bước D: Tạo Tour ---
            $tour = Tour::create($tourData);

            // --- Bước E: Lưu Làng nghề (Bảng trung gian) ---
            // attach() sẽ thêm các dòng vào bảng tour_village
            $tour->villages()->attach($validatedData['villages']);

            // --- Bước F: Lưu Lịch khởi hành ---
            if ($request->has('schedules')) {
                // Lọc bỏ các hàng rỗng (nếu JS gửi lên hàng trống)
                $validSchedules = collect($validatedData['schedules'])->filter(function ($s) {
                    return !empty($s['start_date']) && !empty($s['end_date']);
                });

                foreach ($validSchedules as $scheduleData) {
                    $tour->schedules()->create([
                        'start_date' => $scheduleData['start_date'],
                        'end_date' => $scheduleData['end_date'],
                        'seats_available' => $scheduleData['seats_available']
                    ]);
                }
            }

            // --- Bước G: Lưu Lịch trình chi tiết ---
            if ($request->has('itineraries')) {
                // Lọc bỏ hàng rỗng
                $validItineraries = collect($validatedData['itineraries'])->filter(function ($i) {
                    return !empty($i['activities']);
                });

                foreach ($validItineraries as $itineraryData) {
                    $tour->itineraries()->create([
                        'day_number' => $itineraryData['day_number'],
                        'activities' => $itineraryData['activities'],
                        'meals' => $itineraryData['meals'] ?? '',
                        'accommodation' => $itineraryData['accommodation'] ?? ''
                    ]);
                }
            }

            // --- Bước H: Hoàn tất ---
            DB::commit();

            // Chuyển hướng về trang chi tiết tour vừa tạo
            return redirect()->route('admin.tour.detail', ['id' => $tour->tour_id])
                ->with('status', 'Thêm tour mới thành công!');
        } catch (\Exception $e) {
            // Nếu có lỗi, hủy toàn bộ thao tác
            DB::rollBack();
            // Ghi log để debug
            Log::error('Lỗi thêm tour: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    /**
     * Chuyển đổi trạng thái: Active <-> Inactive
     */
    public function toggleStatus($id)
    {
        try {
            $tour = Tour::findOrFail($id);

            $tour->status = $tour->status === 'active' ? 'inactive' : 'active';
            $tour->save();

            $message = $tour->status === 'active'
                ? 'Đã kích hoạt tour thành công!'
                : 'Đã ngừng hoạt động tour!';

            return redirect()->back()->with('status', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()]);
        }
    }

    public function deleteTour($id)
    {
        // Lấy tour
        $tour = Tour::where('tour_id', $id)->firstOrFail();

        // Kiểm tra tour có booking hay không
        $hasBooking = $tour->bookings()->exists();

        if ($hasBooking) {
            // Chỉ cập nhật trạng thái thay vì xóa
            $tour->status = 'inactive'; // Ngừng hoạt động
            $tour->save();

            return redirect()->back()->with('status', 'Tour đang có booking nên chỉ được chuyển sang trạng thái ngừng hoạt động.');
        }

        // ============================
        // Nếu KHÔNG có booking → XÓA
        // ============================

        // Xoá lịch trình (itineraries)
        $tour->itineraries()->delete();

        // Xoá lịch khởi hành (schedules)
        $tour->schedules()->delete();

        // Xóa record trong bảng trung gian tour_village
        $tour->villages()->detach();

        // Cuối cùng xoá tour
        $tour->delete();

        return redirect()->back()->with('status', 'Xóa tour thành công!');
    }
}
