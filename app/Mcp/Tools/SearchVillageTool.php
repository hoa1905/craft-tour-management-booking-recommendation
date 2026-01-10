<?php

namespace App\Mcp\Tools;

use App\Models\Village;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly()]
class SearchVillageTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $name = 'search_villages';
    protected string $description = 'Tìm kiếm thông tin làng nghề truyền thống theo tên hoặc theo vùng miền (Bắc, Trung, Nam).';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        // 1. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'keyword' => 'nullable|string', // Ví dụ: Gốm, Lụa
            'region'  => 'nullable|string', // Ví dụ: Miền Bắc, Hà Nội
        ]);

        $query = Village::query();

        // 2. Logic tìm theo tên làng nghề
        if (!empty($validated['keyword'])) {
            $query->where('village_name', 'like', "%{$validated['keyword']}%");
        }

        // 3. Logic tìm theo Vùng miền (Quan trọng cho câu hỏi "Miền Bắc có làng nào")
        if (!empty($validated['region'])) {
            // Tìm trong bảng Province (Tỉnh) hoặc Region (Vùng) thông qua quan hệ
            $query->whereHas('province', function ($q) use ($validated) {
                $q->where('province_name', 'like', "%{$validated['region']}%")
                  ->orWhereHas('region', function ($r) use ($validated) {
                      $r->where('region_name', 'like', "%{$validated['region']}%");
                  });
            });
        }

        // 4. Lấy dữ liệu (Giới hạn 4 kết quả để không quá dài)
        $villages = $query->limit(4)->get();

        if ($villages->isEmpty()) {
            return Response::text("Không tìm thấy làng nghề nào phù hợp với yêu cầu.");
        }

        // 5. Format dữ liệu trả về cho AI (Kèm Link chi tiết)
        $result = $villages->map(function ($village) {
            return [
                'name' => $village->village_name,
                'address' => $village->address,
                'link' => route('villages.detail', ['id' => $village->village_id]),
                'desc' => $village->description
            ];
        });

        return Response::text($result->toJson());
    }
    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema($schema): array
    {
        return [
            'keyword' => $schema->string()
                ->description('Tên làng nghề hoặc sản phẩm (Ví dụ: Gốm, Vạn Phúc).'),
            
            'region' => $schema->string()
                ->description('Tên vùng miền hoặc tỉnh thành (Ví dụ: Miền Bắc, Đà Nẵng).'),
        ];
    }
}
