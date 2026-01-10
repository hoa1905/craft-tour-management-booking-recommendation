<?php

namespace App\Mcp\Tools;

use App\Models\Tour;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class SearchTourTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $name = 'search_tours';
    protected string $description = 'Tìm kiếm thông tin tour du lịch dựa trên từ khóa.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        // 1. Validate: Cho phép keyword null nếu có tìm theo giá
        $validated = $request->validate([
            'keyword' => 'nullable|string',
            'max_price' => 'nullable|numeric'
        ]);

        $query = Tour::query();

        // Logic tìm kiếm linh hoạt hơn
        if (!empty($validated['keyword'])) {
            $query->where('tour_name', 'like', "%{$validated['keyword']}%");
        }

        // Nếu có giá thì lọc theo giá
        if (!empty($validated['max_price'])) {
            $query->where('price_adult', '<=', $validated['max_price']);
        }

        // Lấy dữ liệu
    $tours = $query->limit(4)->get(['tour_id', 'tour_name', 'price_adult', 'description']);

    if ($tours->isEmpty()) {
        return Response::text("Không tìm thấy tour nào phù hợp.");
    }

    // Tạo link chi tiết cho từng tour
    $result = $tours->map(function ($tour) {
        return [
            'name' => $tour->tour_name,
            'price' => number_format($tour->price_adult) . ' VNĐ',
            'link' => route('tour.detail', ['id' => $tour->tour_id]),
            'desc' => $tour->description
        ];
    });

    // Trả về JSON chứa Link
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
                ->description('Tên địa điểm hoặc tên tour (Ví dụ: Hội An, Gốm).'), // Bỏ required()
            
            // 2. Thêm định nghĩa Schema cho giá
            'max_price' => $schema->number()
                ->description('Mức giá tối đa mà khách muốn tìm (VNĐ). Ví dụ: 200000'),
        ];
    }
}
