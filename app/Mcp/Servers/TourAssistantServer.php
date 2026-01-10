<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CheckBookingTool;
use App\Mcp\Tools\RealtimeSearchTool;
use App\Mcp\Tools\RecommendTourTool;
use App\Mcp\Tools\SearchTourTool;
use App\Mcp\Tools\SearchVillageTool;
use Laravel\Mcp\Server;

class TourAssistantServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Tour Assistant Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Instructions describing how to use the server and its features.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        SearchTourTool::class,
        CheckBookingTool::class,
        RecommendTourTool::class,
        RealtimeSearchTool::class,
        SearchVillageTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
