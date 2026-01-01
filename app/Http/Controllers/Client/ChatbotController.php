<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Http, Log, Auth, Session};
use Gemini\Laravel\Facades\Gemini;
use App\Models\{Tour, Booking};

/**
 * MCP HOST: Laravel Framework
 * MCP CLIENT: ChatbotController
 */
class ChatbotController extends Controller
{
    private string $model;
    private string $flaskServerUrl;

    public function __construct()
    {
        $this->model = config('gemini.default_model', 'gemini-1.5-flash');
        // Địa chỉ MCP Server xử lý thuật toán AI (Flask)
        $this->flaskServerUrl = "http://localhost:5555/api";
    }

    /**
     * LUỒNG XỬ LÝ CHÍNH (The MCP Workflow)
     */
    public function handleChat(Request $request)
    {
        $message = $request->input('message');
        $history = Session::get('chat_history', []);
        $context = "";

        try {
            // BƯỚC 1 & 2: INTENT DISCOVERY (Hỏi LLM cần dùng Tool nào)
            $intent = $this->discoverIntent($message);
            Log::info("MCP Discovery: User wants to use Tool [$intent]");

            // BƯỚC 3: TOOL EXECUTION (Gọi các MCP Servers)
            $context = $this->executeMcpTool($intent, $message);

            // BƯỚC 4: CONTEXT AUGMENTATION & GENERATION (LLM trả lời dựa trên Context)
            $aiResponse = $this->generateFinalResponse($message, $history, $context);

            // CẬP NHẬT TRẠNG THÁI (State Management)
            $this->saveToHistory($message, $aiResponse);

            return response()->json([
                'reply' => $aiResponse,
                'intent' => $intent,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error("MCP Workflow Error: " . $e->getMessage());
            return response()->json(['reply' => "Hệ thống MCP đang gặp sự cố kết nối."], 500);
        }
    }

    // =========================================================================
    // MCP CLIENT LOGIC (ĐIỀU PHỐI)
    // =========================================================================

    private function discoverIntent($message)
    {
        $prompt = "Phân loại câu hỏi vào 1 nhãn Tool duy nhất: 
                  - USER_RECOMMENDATION: Gợi ý tour theo sở thích cá nhân.
                  - TOUR_SEARCH: Tìm thông tin tour chung.
                  - BOOKING_INQUIRY: Kiểm tra đơn hàng.
                  - REALTIME_SEARCH: Tin tức, thời tiết, sự kiện bên ngoài.
                  - GENERAL: Chào hỏi.
                  Câu hỏi: '$message'";

        $res = Gemini::generativeModel(model: $this->model)->generateContent($prompt);
        return trim(str_replace(['*', ' '], '', $res->text()));
    }

    private function executeMcpTool($intent, $message)
    {
        // Kiểm tra quyền truy cập cho các Tool cá nhân hóa
        if (in_array($intent, ['USER_RECOMMENDATION', 'BOOKING_INQUIRY']) && !Auth::guard('client')->check()) {
            return "THÔNG BÁO: Người dùng chưa đăng nhập. Bạn hãy yêu cầu khách đăng nhập để sử dụng tính năng này.";
        }

        switch ($intent) {
            case 'USER_RECOMMENDATION':
                return $this->serverFlaskRecommendation(Auth::guard('client')->id());
            case 'TOUR_SEARCH':
                return $this->serverDatabaseTours($message);
            case 'BOOKING_INQUIRY':
                return $this->serverDatabaseBookings(Auth::guard('client')->id());
            case 'REALTIME_SEARCH':
                return $this->serverExternalSerper($message);
            default:
                return "";
        }
    }

    // =========================================================================
    // MCP SERVERS (CUNG CẤP DỮ LIỆU)
    // =========================================================================

    /**
     * SERVER: AI Recommendation (Python Flask)
     * Phương thức: Tool tính toán cá nhân hóa
     */
    private function serverFlaskRecommendation($userId)
    {
        $response = Http::get("{$this->flaskServerUrl}/user-recommendations", ['user_id' => $userId]);
        if ($response->successful()) {
            $ids = $response->json('recommended_tours');
            $tours = Tour::whereIn('tour_id', $ids)->get(['tour_name', 'price_adult', 'description']);
            return "Gợi ý từ AI Server (JSON): " . $tours->toJson();
        }
        return "Server AI hiện không phản hồi.";
    }

    /**
     * SERVER: MySQL Database
     * Phương thức: Source dữ liệu tour
     */
    private function serverDatabaseTours($message)
    {
        $tours = Tour::where('tour_name', 'like', "%$message%")->limit(3)->get();
        return "Dữ liệu thực tế từ hệ thống: " . $tours->toJson();
    }

    /**
     * SERVER: MySQL Database
     * Phương thức: Source dữ liệu cá nhân
     */
    private function serverDatabaseBookings($userId)
    {
        $bookings = Booking::where('user_id', $userId)->with('tour')->latest()->limit(1)->get();
        return "Thông tin đơn hàng gần nhất của khách: " . $bookings->toJson();
    }

    /**
     * SERVER: Serper.dev API
     * Phương thức: Tool thông tin Internet
     */
    private function serverExternalSerper($message)
    {
        $res = Http::withHeaders(['X-API-KEY' => config('services.serper.key')])
            ->post('https://google.serper.dev/search', ['q' => $message]);
        return "Thông tin cập nhật từ Internet: " . ($res->json('organic.0.snippet') ?? "N/A");
    }

    // =========================================================================
    // FINAL RESPONSE GENERATION
    // =========================================================================

    private function generateFinalResponse($message, $history, $context)
    {
        $historyString = "";
        foreach (array_slice($history, -6) as $msg) {
            $historyString .= $msg['role'] . ": " . $msg['content'] . "\n";
        }

        // Kiểm tra trạng thái đăng nhập của khách
        $isLoggedIn = Auth::guard('client')->check();
        $userStatus = $isLoggedIn ? "Đã đăng nhập" : "Chưa đăng nhập";

        $finalPrompt = "Bạn là Trợ lý Hành Trình Vàng. Hãy dựa vào ngữ cảnh sau để trả lời người dùng.
                   ---
                   TRẠNG THÁI NGƯỜI DÙNG: $userStatus
                   LỊCH SỬ: $historyString
                   NGỮ CẢNH DỮ LIỆU: $context
                   CÂU HỎI: $message
                   ---
                   Yêu cầu: 
                   1. Nếu có dữ liệu JSON tour, hãy tóm tắt chuyên nghiệp, trình bày đẹp.
                   2. Nếu TRẠNG THÁI là 'Chưa đăng nhập' và dữ liệu yêu cầu quyền riêng tư, hãy nhắc khách đăng nhập lịch sự.
                   3. Nếu TRẠNG THÁI là 'Đã đăng nhập', TUYỆT ĐỐI KHÔNG yêu cầu người dùng đăng nhập nữa.";

        $res = Gemini::generativeModel(model: $this->model)->generateContent($finalPrompt);
        return $res->text();
    }

    private function saveToHistory($user, $ai)
    {
        $history = Session::get('chat_history', []);
        $history[] = ['role' => 'user', 'content' => $user];
        $history[] = ['role' => 'model', 'content' => $ai];
        Session::put('chat_history', array_slice($history, -10));
    }
}
