<?php

namespace App\Mcp\Tools;

use App\Models\Tour;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class RecommendTourTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $name = 'recommend_tours';
    protected string $description = 'Gợi ý tour cá nhân hóa dựa trên sở thích người dùng (AI).';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = Auth::guard('client')->user();
        if (!$user) return Response::error('Cần đăng nhập để nhận gợi ý.');

        // Gọi sang Flask Server
        $flaskUrl = "http://localhost:5555/api/user-recommendations";
        $apiRes = Http::get($flaskUrl, ['user_id' => $user->id]);

        if ($apiRes->successful()) {
            $ids = $apiRes->json('recommended_tours');
            $tours = Tour::whereIn('tour_id', $ids)->get(['tour_name', 'price_adult']);
            return Response::text("Gợi ý riêng cho bạn: " . $tours->toJson());
        }

        return Response::error("Hệ thống gợi ý đang bảo trì.");
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema($schema): array
    {
        return [];
    }
}
