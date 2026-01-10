<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Session, Log};
use Gemini\Laravel\Facades\Gemini;

use Gemini\Data\Tool;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema as GeminiSchema; 
use Gemini\Enums\DataType;

use App\Mcp\Tools\{SearchTourTool, CheckBookingTool, RecommendTourTool, RealtimeSearchTool, SearchVillageTool};
use Gemini\Data\Content;
use Gemini\Enums\Role;
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
        // lấy lịch sử từ session, truyền vào Gemini để duy trì ngữ cảnh
        $rawHistory = Session::get('chat_history', []);
        $history = [];
        foreach ($rawHistory as $msg) {
            $roleEnum = ($msg['role'] === 'user') ? Role::USER : Role::MODEL;
            $history[] = Content::parse(part: $msg['content'], role: $roleEnum);
        }

        try {
            $systemInstruction = "Bạn là trợ lý du lịch. Nhiệm vụ của bạn là hỗ trợ khách hàng tìm tour." .
            "QUY TẮC TRẢ LỜI QUAN TRỌNG:\n" .
            "1. Khi giới thiệu tour, BẮT BUỘC phải cung cấp đường link chi tiết.\n" .
            "2. Định dạng link theo cú pháp Markdown: [Tên Tour - Xem chi tiết](URL).\n" .
            "3. Ví dụ: Bạn nên xem thử [Tour Làng Gốm Bát Tràng](http://yourdomain.com/tour/1) nhé.\n" .
            "4. Giọng điệu thân thiện, mời gọi khách đặt tour.";

            $chat = Gemini::generativeModel(model: $this->model)
                ->withSystemInstruction(Content::parse(part: $systemInstruction))
                ->withTool($this->getGeminiToolsDefinition())
                 // Truyền lịch sử vào đây
                ->startChat(history: $history);

            $response = $chat->sendMessage($userMessage);

            // Lấy function call an toàn
            $parts = $response->parts();
            $firstPart = $parts[0] ?? null;
            $functionCall = $firstPart ? $firstPart->functionCall : null;

            if ($functionCall) {
                // Trường hợp AI gọi Tool
                $toolName = $functionCall->name;
                $toolArgs = $functionCall->args;

                Log::info("AI GỌI TOOL:", ['Name' => $toolName, 'Args' => $toolArgs]);

                // Thực thi Tool Logic
                $toolResult = $this->executeMcpToolClass($toolName, $toolArgs);
                Log::info("KẾT QUẢ TỪ TOOL:", ['Result' => substr($toolResult, 0, 100) . '...']);

                // Gửi kết quả về cho AI
                $finalResponse = $chat->sendMessage(
                    "Kết quả từ Tool (System Context): " . $toolResult
                );
                
                // lấy text an toàn, nối chuỗi từ các parts để tránh lỗi
                $aiReply = '';
                foreach ($finalResponse->parts() as $part) {
                    $aiReply .= $part->text ?? '';
                }

            } else {
                // Trương hợp AI trả lời bình thường
                $aiReply = '';
                foreach ($response->parts() as $part) {
                    $aiReply .= $part->text ?? '';
                }
            }

            // Lưu lịch sử
            $this->saveToHistory($userMessage, $aiReply);

            return response()->json(['reply' => $aiReply]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
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
                    description: 'Kiểm tra lịch sử đơn hàng. Có thể tìm đơn hàng theo trạng thái cụ thể (hủy, thành công...).',
                    parameters: new GeminiSchema(
                        type: DataType::OBJECT,
                        properties: [
                            'status' => new GeminiSchema(
                                type: DataType::STRING,
                                description: 'Trạng thái đơn hàng cần tìm (VD: canceled, paid, pending)".'
                            ),
                        ],
                        required: [] // Không bắt buộc, nếu không có sẽ lấy danh sách chung
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
                
                // 5. Tool tìm kiếm làng nghề
                new FunctionDeclaration(
                name: 'search_villages',
                description: 'Tìm kiếm làng nghề theo tên hoặc khu vực (Miền Bắc, Trung, Nam)',
                parameters: new GeminiSchema(
                    type: DataType::OBJECT,
                    properties: [
                        'keyword' => new GeminiSchema(
                            type: DataType::STRING,
                            description: 'Tên làng nghề (Ví dụ: Gốm, Lụa)'
                        ),
                        'region' => new GeminiSchema(
                            type: DataType::STRING,
                            description: 'Khu vực địa lý (Ví dụ: Miền Bắc, Huế)'
                        ),
                    ],
                    required: []
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
            'search_villages' => new SearchVillageTool(),
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