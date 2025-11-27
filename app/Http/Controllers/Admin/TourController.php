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
        // 1. Định nghĩa quy tắc
        $rules = [
            'tour_name' => 'required|string|max:255',
            'transport' => 'required|string|max:100',
            'price_adult' => 'required|numeric|min:0',
            'price_child' => 'required|numeric|min:0',
            'departure_point' => 'required|string',
            'duration' => 'required|string|max:100',
            'description' => 'required|string',

            // Ảnh không bắt buộc (nullable) khi sửa
            'image_main' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

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
            'image_main.required' => 'Vui lòng chọn ảnh đại diện.',
            'image_main.mimes' => 'Ảnh chính phải có định dạng: jpeg, png, jpg, gif.',
            'image_main.max' => 'Ảnh chính vượt quá dung lượng cho phép (2MB).',

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
            $imagePath = public_path('images/tours');
            if ($request->hasFile('image_main')) {
                // Xóa ảnh cũ
                if ($tour->image_main && File::exists($imagePath . '/' . $tour->image_main)) {
                    File::delete($imagePath . '/' . $tour->image_main);
                }
                // Upload ảnh mới
                $file = $request->file('image_main');
                $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                $file->move($imagePath, $filename);
                $tour->image_main = $filename;
            }

            // 8. Xứ lý Gallery
            // Mảng ảnh hiện tại trong DB (trước khi update)
            $oldGallery = $tour->gallery ?? [];
            // Mảng ảnh người dùng muốn giữ lại (lấy từ input hidden)
            $keptGallery = $request->input('existing_gallery', []);

            // --- Bước 8a: Tìm và Xóa ảnh vật lý ---
            // Lấy những ảnh có trong Old nhưng không có trong Kept => Ảnh cần xóa
            $imagesToDelete = array_diff($oldGallery, $keptGallery);

            foreach ($imagesToDelete as $deleteImage) {
                $filePath = $imagePath . '/' . $deleteImage;
                if (File::exists($filePath)) {
                    File::delete($filePath); // Xóa file khỏi thư mục
                }
            }

            // --- Bước 8b: Upload ảnh mới và gộp mảng ---
            // Bắt đầu gallery mới bằng những ảnh được giữ lại
            $finalGallery = $keptGallery;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $file) {
                    $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                    $file->move($imagePath, $filename);

                    // Thêm ảnh mới vào mảng
                    $finalGallery[] = $filename;
                }
            }

            // Lưu mảng mới vào DB (Laravel tự cast sang JSON)
            $tour->gallery = array_values($finalGallery); // array_values để đánh lại index (0,1,2...)

            // Cập nhật các trường cơ bản của Tour
            $tour->update($updateData);

            // 9. Đồng bộ hóa Làng nghề (Many-to-Many)
            // -----------------------------------------------------
            // sync() sẽ tự động xóa các làng nghề không có trong $request
            // và thêm các làng nghề mới.
            $tour->villages()->sync($request->input('villages', []));

            // 10. Đồng bộ hóa Lịch khởi hành (One-to-Many)
            // -----------------------------------------------------
            // Cách an toàn nhất: Xóa hết cái cũ, tạo lại từ request
            $tour->schedules()->delete();
            if ($request->has('schedules')) {
                foreach ($validatedData['schedules'] as $scheduleData) {
                    $tour->schedules()->create($scheduleData);
                }
            }

            // 11. Đồng bộ hóa Lịch trình (One-to-Many)
            // -----------------------------------------------------
            // Tương tự, xóa hết và tạo lại
            $tour->itineraries()->delete();
            if ($request->has('itineraries')) {
                foreach ($validatedData['itineraries'] as $itineraryData) {
                    $tour->itineraries()->create($itineraryData);
                }
            }

            // 12. Hoàn tất
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
        // Lấy danh sách làng nghề (kèm tỉnh, miền) nhưng chỉ lấy cái nào đang 'active'
        $villages = Village::with('province.region')
            ->where('status', 'active')
            ->get();

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
            'image_main' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
            'images' => 'nullable|array', // Gallery
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

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
            'image_main.max' => 'Ảnh không được vượt quá 2MB.',

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

        // 2. Xử lý logic và lưu database (transaction)
        DB::beginTransaction(); // Bắt đầu giao dịch
        try {

            // --- 1. Tính toán "Điểm đến" (Destination) ---
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

            // --- 2. Chuẩn bị dữ liệu Tour ---
            $tourData = [
                'tour_name' => $validatedData['tour_name'],
                'transport' => $validatedData['transport'],
                'price_adult' => $validatedData['price_adult'],
                'price_child' => $validatedData['price_child'],
                'departure_point' => $validatedData['departure_point'],
                'destination' => $destination_string,
                'duration' => $validatedData['duration'],
                'description' => $validatedData['description'],
                'status' => 'active',
                'gallery' => [], // Khởi tạo mảng rỗng
            ];

            // 3. Xử lý upload ảnh chính
            $imagePath = public_path('images/tours');
            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            if ($request->hasFile('image_main')) {
                $file = $request->file('image_main');
                $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                $file->move($imagePath, $filename);
                $tourData['image_main'] = $filename;
            }

            // 4. Xử lý upload thư viện ảnh (Gallery)
            if ($request->hasFile('images')) {
                $galleryPaths = [];
                foreach ($request->file('images') as $key => $file) {
                    $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                    $file->move($imagePath, $filename);
                    $galleryPaths[] = $filename;
                }
                $tourData['gallery'] = $galleryPaths; // Model đã cast sang array, nên lưu mảng trực tiếp
            }

            // --- 5. Tạo Tour ---
            $tour = Tour::create($tourData);

            // --- 6. Lưu Làng nghề (Bảng trung gian) ---
            // attach() sẽ thêm các dòng vào bảng tour_village
            $tour->villages()->attach($validatedData['villages']);

            // --- 7. Lưu Lịch khởi hành ---
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

            // --- 8.Lưu Lịch trình chi tiết ---
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

            // --- 9. Hoàn tất ---
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
