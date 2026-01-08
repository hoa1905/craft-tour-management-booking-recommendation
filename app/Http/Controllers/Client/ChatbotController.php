<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Session, Auth, Log};
use Gemini\Laravel\Facades\Gemini;

use Gemini\Data\Tool;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema as GeminiSchema; 
use Gemini\Enums\DataType;

use App\Mcp\Tools\{SearchTourTool, CheckBookingTool, RecommendTourTool, RealtimeSearchTool};
use Laravel\Mcp\Request as McpRequest;

class ChatbotController extends Controller
{
    private string $model;

    public function __construct()
    {
        $this->model = config('gemini.default_model', 'gemini-1.5-flash');
    }

    public function handleChat(Request $request)
    {
        $userMessage = $request->input('message');
        $history = [];

        try {
            // Khởi tạo Chat Session
            $chat = Gemini::generativeModel(model: $this->model)
                ->withTool($this->getGeminiToolsDefinition())
                ->startChat(history: $history);

            // Gửi tin nhắn
            $response = $chat->sendMessage($userMessage);

            // 2. Lấy function call từ phần đầu tiên của response
            // Gemini trả về response dạng các "parts". Function call nằm trong part đầu tiên.
            $parts = $response->parts();
            $firstPart = $parts[0] ?? null;
            $functionCall = $firstPart ? $firstPart->functionCall : null;

            if ($functionCall) {
                // AI muốn gọi Tool
                $toolName = $functionCall->name;
                 // args này thường đã là mảng hoặc object
                $toolArgs = $functionCall->args;

                // === LOG ===
                Log::info("AI QUYẾT ĐỊNH GỌI TOOL:", [
                    'Tool Name' => $toolName,
                    'Arguments' => $toolArgs
                ]);

                // Thực thi Tool Logic
                $toolResult = $this->executeMcpToolClass($toolName, $toolArgs);

                // Ghi log kết quả trả về từ Tool
                Log::info("KẾT QUẢ TỪ TOOL:", ['Result' => substr($toolResult, 0, 100) . '...']);

                // Gửi kết quả về cho AI
                $finalResponse = $chat->sendMessage(
                    "Kết quả từ Tool (System Context): " . $toolResult
                );
                
                $aiReply = $finalResponse->text();
            } else {
                // AI trả lời bình thường
                $aiReply = $response->text();
            }

            $this->saveToHistory($userMessage, $aiReply);

            return response()->json(['reply' => $aiReply]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Định nghĩa Tools
     */
    private function getGeminiToolsDefinition(): Tool
    {
       return new Tool(
            functionDeclarations: [
                // 1. Tool tìm kiếm tour
                new FunctionDeclaration(
                    name: 'search_tours',
                    description: 'Tìm kiếm tour theo tên hoặc giá tiền',
                    parameters: new GeminiSchema(
                        type: DataType::OBJECT,
                        properties: [
                            'keyword' => new GeminiSchema(
                                type: DataType::STRING,
                                description: 'Tên địa điểm hoặc tên tour (Ví dụ: Hội An, Gốm).'
                            ),
                            'max_price' => new GeminiSchema(
                                type: DataType::NUMBER,
                                description: 'Mức giá tối đa mà khách muốn tìm (VNĐ). Ví dụ: 200000'
                            ),
                        ],
                        required: []
                    )
                ),

                // 2. Tool kiểm tra đơn hàng
                new FunctionDeclaration(
                    name: 'check_booking',
                    description: 'Kiểm tra đơn hàng của tôi',
                    parameters: new GeminiSchema(
                        type: DataType::OBJECT,
                        properties: []
                    )
                ),

                // 3. Tool gợi ý
                new FunctionDeclaration(
                    name: 'recommend_tours',
                    description: 'Gợi ý tour cá nhân hóa',
                    parameters: new GeminiSchema(
                        type: DataType::OBJECT,
                        properties: []
                    )
                ),

                // 4. Tool tìm kiếm Internet
                new FunctionDeclaration(
                    name: 'realtime_search',
                    description: 'Tra cứu thông tin bên ngoài',
                    parameters: new GeminiSchema(
                        type: DataType::OBJECT,
                        properties: [
                            'query' => new GeminiSchema(
                                type: DataType::STRING,
                                description: 'Câu hỏi cần tìm'
                            ),
                        ],
                        required: ['query']
                    )
                ),
            ]
        );
    }

    private function executeMcpToolClass($toolName, $args)
    {
        $toolClass = match ($toolName) {
            'search_tours' => new SearchTourTool(),
            'check_booking' => new CheckBookingTool(),
            'recommend_tours' => new RecommendTourTool(),
            'realtime_search' => new RealtimeSearchTool(),
            default => null
        };

        if (!$toolClass) return "Tool không tồn tại.";

        // Ép kiểu args về mảng
        $argsArray = is_array($args) ? $args : (array)$args;
        
        // Chỉ khởi tạo Request với tham số
        $mcpRequest = new McpRequest($argsArray);
        
        // Gọi hàm handle
        return $toolClass->handle($mcpRequest)->content();
    }

    private function saveToHistory($user, $ai) 
    {
        $history = Session::get('chat_history', []);
        $history[] = ['role' => 'user', 'content' => $user];
        $history[] = ['role' => 'model', 'content' => $ai];
        Session::put('chat_history', array_slice($history, -10));
    }
}