<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminVillageController extends Controller
{
    // Trong class AdminVillageController

    public function villageDetail($id)
    {
        // Lấy thông tin làng nghề kèm theo Tỉnh -> Miền và các Tour liên quan
        $village = Village::with([
            'province.region',
            'tours' => function ($q) {
                $q->where('status', 'active');
            }
        ])->findOrFail($id);

        return view('admin.villages.detail', compact('village'));
    }

    public function index()
    {
        // Eager loading: Lấy Village kèm theo Province, và từ Province lấy tiếp Region
        // Dấu chấm . trong 'province.region' biểu thị sự lồng nhau
        // Dùng paginate để phân trang
        $villages = Village::with('province.region')->paginate(10);

        return view('admin.villages.list', compact('villages'));
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function showEditForm($id)
    {
        $village = Village::findOrFail($id);
        // Lấy danh sách tỉnh kèm miền để JS xử lý
        $provinces = Province::with('region')->get();

        return view('admin.villages.edit', compact('village', 'provinces'));
    }

    /**
     * Cập nhật dữ liệu
     */
    public function updateVillage(Request $request, $id)
    {
        // 1. Validate
        $request->validate([
            'village_name' => 'required|string|max:150',
            'province_id' => 'required|exists:provinces,province_id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string|max:255',
            'description' => 'required|string',

            // Ảnh không bắt buộc (nullable) khi sửa
            'image_main' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'village_name.required' => 'Vui lòng nhập tên làng nghề.',
            'province_id.required' => 'Vui lòng chọn tỉnh.',
            'latitude.required' => 'Vui lòng nhập vĩ độ.',
            'longitude.required' => 'Vui lòng nhập kinh độ.',
            'address.required' => 'Vui lòng nhập địa điểm cụ thể.',
            'description.required' => 'Vui lòng nhập mô tả làng nghề truyền thống.',
        ]);

        $village = Village::findOrFail($id);

        DB::beginTransaction();
        try {
            // 2. Cập nhật thông tin cơ bản
            $village->village_name = $request->village_name;
            $village->province_id  = $request->province_id;
            $village->latitude     = $request->latitude;
            $village->longitude    = $request->longitude;
            $village->address      = $request->address;
            $village->description  = $request->description;

            $imagePath = public_path('images/villages');

            // 3. Xử lý ảnh đại diện (Xóa ảnh cũ nếu có thay đổi)
            if ($request->hasFile('image_main')) {
                // Xóa ảnh cũ
                if ($village->image_main && File::exists($imagePath . '/' . $village->image_main)) {
                    File::delete($imagePath . '/' . $village->image_main);
                }
                // Upload ảnh mới
                $file = $request->file('image_main');
                $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                $file->move($imagePath, $filename);
                $village->image_main = $filename;
            }

            // 4. Xứ lý Gallery 
            // Mảng ảnh hiện tại trong DB (trước khi update)
            $oldGallery = $village->gallery ?? [];
            // Mảng ảnh người dùng muốn giữ lại (lấy từ input hidden)
            $keptGallery = $request->input('existing_gallery', []);

            // --- Bước 4a: Tìm và Xóa ảnh vật lý ---
            // Lấy những ảnh có trong Old nhưng không có trong Kept => Ảnh cần xóa
            $imagesToDelete = array_diff($oldGallery, $keptGallery);

            foreach ($imagesToDelete as $deleteImage) {
                $filePath = $imagePath . '/' . $deleteImage;
                if (File::exists($filePath)) {
                    File::delete($filePath); // Xóa file khỏi thư mục
                }
            }

            // --- Bước 4b: Upload ảnh mới và gộp mảng ---
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
            $village->gallery = array_values($finalGallery); // array_values để đánh lại index (0,1,2...)

            $village->save();

            DB::commit();
            return redirect()->route('admin.village.detail', ['id' => $village->village_id])->with('status', 'Cập nhật làng nghề thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật làng nghề: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function showAddVillageForm()
    {
        // Cần gửi $provinces và $villages rỗng để JS-picker hoạt động
        $provinces = Province::all();
        $villages = Village::with('province.region')->get();

        // Trả về view 'add', không cần $tour
        return view('admin.villages.add', compact('provinces', 'villages'));
    }

    // Xử lý lưu dữ liệu
    public function addVillage(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'village_name' => 'required|string|max:150',
            'province_id' => 'required|exists:provinces,province_id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255', // Địa điểm cụ thể
            'description' => 'required|string',
            'image_main' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images' => 'nullable|array', // Gallery
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'village_name.required' => 'Vui lòng nhập tên làng nghề.',
            'province_id.required' => 'Vui lòng chọn tỉnh.',
            'latitude.required' => 'Vui lòng nhập vĩ độ.',
            'longitude.required' => 'Vui lòng nhập kinh độ.',
            'address.required' => 'Vui lòng nhập địa điểm cụ thể.',
            'image_main.required' => 'Vui lòng chọn ảnh đại diện.',
            'description.required' => 'Vui lòng nhập mô tả làng nghề truyền thống.',
        ]);

        DB::beginTransaction();
        try {
            $villageData = [
                'village_name' => $validated['village_name'],
                'province_id'  => $validated['province_id'],
                'latitude'     => $validated['latitude'],
                'longitude'    => $validated['longitude'],
                'address'      => $validated['address'],
                'description'  => $validated['description'],
                'status'       => 'active',
                'gallery'      => [], // Khởi tạo mảng rỗng
            ];

            // 2. Xử lý upload ảnh chính
            $imagePath = public_path('images/villages');
            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            if ($request->hasFile('image_main')) {
                $file = $request->file('image_main');
                $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                $file->move($imagePath, $filename);
                $villageData['image_main'] = $filename;
            }

            // 3. Xử lý upload thư viện ảnh (Gallery)
            if ($request->hasFile('images')) {
                $galleryPaths = [];
                foreach ($request->file('images') as $key => $file) {
                    $filename = time() . '-' . uniqid() . '-' . $file->getClientOriginalName();
                    $file->move($imagePath, $filename);
                    $galleryPaths[] = $filename;
                }
                $villageData['gallery'] = $galleryPaths; // Model đã cast sang array, nên lưu mảng trực tiếp
            }

            // 4. Tạo mới và Lấy đối tượng vừa tạo
            $village = Village::create($villageData);
            DB::commit();

            return redirect()->route('admin.village.detail', ['id' => $village->village_id])
                ->with('status', 'Thêm làng nghề thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi thêm làng nghề: ' . $e->getMessage());

            // (Tùy chọn) Xóa ảnh đã upload nếu lỗi

            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Chuyển đổi trạng thái: Active <-> Inactive
     */
    public function toggleStatus($id)
    {
        try {
            $village = Village::findOrFail($id);

            $village->status = $village->status === 'active' ? 'inactive' : 'active';
            $village->save();

            $message = $village->status === 'active'
                ? 'Đã kích hoạt làng nghề thành công!'
                : 'Đã ngừng hoạt động làng nghề!';

            return redirect()->back()->with('status', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa làng nghề (Hoặc chuyển trạng thái nếu có tour đến làng nghề đó)
     */
    public function deleteVillage($id)
    {
        $village = Village::findOrFail($id);

        // 1. KIỂM TRA: Làng nghề có đang thuộc Tour nào không?
        if ($village->tours()->exists()) {
            // --- TRƯỜNG HỢP 1: ĐANG CÓ TOUR SỬ DỤNG ---

            // Chỉ chuyển trạng thái sang 'inactive' (Ngừng hoạt động)
            $village->status = 'inactive';
            $village->save();

            // Trả về thông báo dạng "Cảnh báo" (warning) để người dùng phân biệt với xóa thành công
            return redirect()->back()->with('warning', 'Làng nghề này đang có trong các Tour nên chỉ được chuyển sang trạng thái "Ngừng hoạt động".');
        }

        // --- TRƯỜNG HỢP 2: KHÔNG CÓ TOUR NÀO SỬ DỤNG ---
        // Tiến hành xóa vĩnh viễn (Xóa ảnh + Xóa DB)

        DB::beginTransaction();
        try {
            $imagePath = public_path('images/villages');

            // A. Xóa ảnh đại diện vật lý
            if ($village->image_main && File::exists($imagePath . '/' . $village->image_main)) {
                File::delete($imagePath . '/' . $village->image_main);
            }

            // B. Xóa thư viện ảnh vật lý (Gallery)
            if (!empty($village->gallery)) {
                foreach ($village->gallery as $img) {
                    if (File::exists($imagePath . '/' . $img)) {
                        File::delete($imagePath . '/' . $img);
                    }
                }
            }

            // C. Xóa record trong database
            // (Không cần detach() vì ở đây chắc chắn không có tour nào liên kết)
            $village->delete();

            DB::commit();

            // Trả về thông báo thành công (status)
            return redirect()->route('admin.village.index')->with('status', 'Đã xóa làng nghề thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa làng nghề: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Lỗi hệ thống, không thể xóa.']);
        }
    }
}
