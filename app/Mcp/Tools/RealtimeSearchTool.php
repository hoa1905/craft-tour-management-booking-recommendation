<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class RealtimeSearchTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $name = 'realtime_search';
    protected string $description = 'Tìm kiếm thông tin thời tiết, tin tức sự kiện thực tế từ Internet.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate(['query' => 'required|string']);
        
        $res = Http::withHeaders(['X-API-KEY' => config('services.serper.key')])
            ->post('https://google.serper.dev/search', ['q' => $validated['query']]);
            
        return Response::text("Kết quả Internet: " . ($res->json('organic.0.snippet') ?? "Không tìm thấy"));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema($schema): array
    {
       return [
            'query' => $schema->string()->description('Câu hỏi hoặc từ khóa cần tìm.')->required()
        ];
    }
}
