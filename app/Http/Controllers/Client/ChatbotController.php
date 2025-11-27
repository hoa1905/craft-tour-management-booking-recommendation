<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Gemini\Laravel\Facades\Gemini;
use App\Models\Tour;
use App\Models\Booking;

class ChatbotController extends Controller
{
    private string $model;

    public function __construct()
    {
        $this->model = config('gemini.default_model');
    }
    /**
     * Điểm tiếp nhận chính (MCP Client gọi đến đây)
     */
    public function handleChat(Request $request)
    {
        $prompt = $request->input('message');
        $history = session('chat_history', []); // Lấy lịch sử (Bộ nhớ)
        $context = ""; // Khởi tạo ngữ cảnh

        try {
            // 1. LLM QUYẾT ĐỊNH (Xác định ý định người dùng)
            $intent = $this->getIntent($prompt);
            Log::info('Chatbot Intent:', ['intent' => $intent]);

            // 2. MCP SERVER GỌI TOOL
            switch ($intent) {
                case 'TOUR_SEARCH':
                    $context = $this->toolSearchTours($prompt);
                    break;
                case 'BOOKING_INQUIRY':
                    $context = $this->toolCheckBooking($prompt);
                    break;
                case 'CUSTOM_TOUR':
                    $context = "Dữ liệu: Khách hàng hỏi về tour tùy chỉnh. Hãy trả lời rằng họ có thể nhấn vào 'Tour theo yêu cầu' trên menu và điền form, chúng tôi sẽ liên hệ lại.";
                    break;
                case 'REALTIME_SEARCH':
                    $context = $this->toolSearchRealtime($prompt);

                    // 🟡 Nếu là REALTIME_SEARCH → trả về ngay (không qua Gemini)
                    return response()->json([
                        'reply' => $context,
                        'intent' => $intent
                    ]);
                default:
                    // Trường hợp 'GENERAL', $context sẽ rỗng (AI tự trả lời)
                    $context = "";
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Tool Error', ['error' => $e->getMessage()]);
            $context = "Lỗi khi gọi tool: " . $e->getMessage();
        }

        // 3. LLM TỔNG HỢP VÀ TRẢ LỜI
        $finalPrompt = $this->buildFinalPrompt($prompt, $history, $context);

        try {
            $response = Gemini::generativeModel(model: $this->model)->generateContent($finalPrompt);
            $aiResponseContent = $response->text() ?? "Xin lỗi, tôi chưa thể hiểu yêu cầu của bạn.";
        } catch (\Exception $e) {
            Log::error('Gemini API Error', ['error' => $e->getMessage()]);
            $aiResponseContent = "Xin lỗi, hiện tại tôi đang gặp sự cố khi xử lý yêu cầu. Vui lòng thử lại sau.";
        }

        // 4. CẬP NHẬT BỘ NHỚ (Lưu lịch sử)
        $history[] = ['role' => 'user', 'content' => $prompt];
        $history[] = ['role' => 'model', 'content' => $aiResponseContent];
        session(['chat_history' => $history]);

        return response()->json(['reply' => $aiResponseContent]);
    }

    /**
     * Tool 1: Phân loại ý định (LLM)
     */
    private function getIntent($prompt)
    {
        $classifierPrompt = <<<PROMPT
        Phân loại câu hỏi thành MỘT trong các loại nhãn sau:
        1. TOUR_SEARCH: Nếu hỏi về thông tin tour, làng nghề, giá vé, lịch trình, thời gian, địa điểm (ví dụ: "tour Bát Tràng bao nhiêu tiền?", "gợi ý tour 3 ngày").
        2. BOOKING_INQUIRY: Nếu hỏi về đơn đặt tour cá nhân, trạng thái đơn hàng, cách hủy tour (ví dụ: "kiểm tra đơn hàng của tôi", "làm sao để hủy tour?").
        3. CUSTOM_TOUR: Nếu hỏi về tour theo yêu cầu riêng.
        4. REALTIME_SEARCH - hỏi thông tin bên ngoài hoặc kiến thức thực tế như: Thời tiết, giá vàng, tin tức, tỷ giá, ai là ai, cái gì là gì, định nghĩa, thông tin sự kiện. (ví dụ: "giá vàng hôm nay", "tình yêu là gì", "ai là tổng bí thư Việt Nam", "Hà Nội ở đâu").
        5. GENERAL - lời chào, hỏi thăm, nói chuyện phiếm không cần thông tin (ví dụ: "xin chào", "bạn khỏe không", "cảm ơn",...).

        Câu hỏi: "$prompt"
        Chỉ trả lời **một nhãn duy nhất** trong danh sách trên. (ví dụ: TOUR_SEARCH, BOOKING_INQUIRY, REALTIME_SEARCH, ...):
        PROMPT;

        try {
            $response = Gemini::generativeModel(model: $this->model)
                ->generateContent($classifierPrompt);
            return trim(str_replace('*', '', $response->text()));
        } catch (\Exception $e) {
            Log::error('Gemini API Error', ['error' => $e->getMessage()]);
            return "Xin lỗi, tôi chưa thể hiểu yêu cầu của bạn.";
        }
    }

    /**
     * Tool 2: Chat với DB - Tours (MCP Server)
     */
    private function toolSearchTours($prompt)
    {
        try {
            // Sử dụng AI để trích xuất từ khóa
            $keywordPrompt = "Trích xuất từ khóa chính để tìm kiếm tour từ câu sau: \"$prompt\". Chỉ trả về từ khóa (ví dụ: 'Bát Tràng', 'Hà Nội 3 ngày', 'giá rẻ').";
            $keywordResponse = Gemini::generativeModel(model: $this->model)
                ->generateContent($keywordPrompt);
            $keyword = trim(str_replace('*', '', $keywordResponse->text()));

            // Tìm trong CSDL (dựa trên schema của bạn)
            $tours = Tour::where('tour_name', 'like', "%$keyword%")
                ->orWhereHas('villages', function ($query) use ($keyword) {
                    $query->where('village_name', 'like', "%$keyword%");
                })
                ->select('tour_name', 'price_adult', 'price_child', 'duration', 'description', 'departure_point')
                ->with('villages:village_name') // Lấy tên các làng nghề
                ->limit(3)
                ->get();

            return $tours->isEmpty()
                ? "Không tìm thấy tour nào khớp với '$keyword'."
                : "Dữ liệu từ CSDL (JSON): " . $tours->toJson();
        } catch (\Exception $e) {
            // Log lỗi để debug
            Log::error('toolSearchTours Error', ['error' => $e->getMessage(), 'prompt' => $prompt]);
            return "Không thể truy vấn dữ liệu tour lúc này. Vui lòng thử lại sau.";
        }
    }

    /**
     * Tool 3: Chat với DB - Bookings (MCP Server)
     */
    private function toolCheckBooking($prompt)
    {
        if (!Auth::guard('client')->check()) {
            return "Người dùng chưa đăng nhập. Hãy yêu cầu họ đăng nhập trước khi hỏi về đơn hàng.";
        }

        $userId = Auth::guard('client')->id();

        // Tìm 2 đơn hàng mới nhất của user
        $bookings = Booking::where('user_id', $userId)
            ->select('booking_id', 'total_price', 'status', 'created_at')
            ->with('tour:tour_id,tour_name')
            ->latest()
            ->limit(2)
            ->get();

        return $bookings->isEmpty()
            ? "Người dùng đã đăng nhập nhưng chưa có đơn hàng."
            : "Dữ liệu từ CSDL (JSON): " . $bookings->toJson();
    }

    /**
     * Tool 4: Chat với API Real-time (MCP Server)
     */
    private function toolSearchRealtime($prompt)
    {
        $response = Http::withHeaders([
            'X-API-KEY' => config('services.serper.key')
        ])->post('https://google.serper.dev/search', [
            'q' => $prompt
        ]);
        if ($response->failed()) return "Lỗi khi gọi API tìm kiếm real-time.";

        $results = $response->json('organic');
        // Lấy tối đa 3 kết quả đầu tiên
        $snippets = array_map(fn($item) => $item['snippet'], array_slice($results, 0, 1));
        return "Dữ liệu real-time từ Google: " . implode(" | ", $snippets);
    }

    /**
     * Xây dựng prompt cuối cùng để gửi cho AI (LLM)
     */
    private function buildFinalPrompt($prompt, $history, $context)
    {
        $historyString = "";
        foreach (array_slice($history, -6) as $msg) {
            // Chỉ lấy 6 tin nhắn cuối: giữ ngữ cảnh hội thoại gần nhất để gửi cho AI, giúp model hiểu được bối cảnh gần nhất, như câu hỏi trước, câu trả lời trước, để trả lời mạch lạc hơn.
            $historyString .= $msg['role'] . ": " . $msg['content'] . "\n";
        }

        return <<<PROMPT
        Bạn là Trợ lý AI của website 'Hành Trình Vàng', chuyên về tour du lịch làng nghề truyền thống Việt Nam.
        Hãy trả lời câu hỏi của người dùng một cách thân thiện, ngắn gọn.

        **Lịch sử chat (Ngữ cảnh hội thoại):**
        $historyString

        **Dữ liệu bổ sung từ Tools (Ngữ cảnh dữ liệu):**
        $context

        **Câu hỏi MỚI của người dùng:**
        "$prompt"

        **Hãy trả lời dựa trên toàn bộ ngữ cảnh ở trên.**
            - Nếu dữ liệu bổ sung là JSON, hãy tóm tắt lại một cách tự nhiên.
            - Nếu dữ liệu là đoạn thông tin (ví dụ kết quả real-time từ Google), hãy viết lại thành một câu hoặc đoạn hoàn chỉnh có ý nghĩa.
            - **Chỉ trả lời đúng trọng tâm câu hỏi, không nói lan man hoặc quảng cáo nếu không liên quan. Hãy tóm tắt chuyên nghiệp, tự nhiên.**

        Câu trả lời của bạn:
        PROMPT;
    }
}
