<?php

use App\Mcp\Servers\TourAssistantServer;
use Laravel\Mcp\Facades\Mcp;

// Đăng ký Server MCP với middleware 'web' để tận dụng Session Auth
Mcp::web('/mcp/tour-assistant', TourAssistantServer::class)
    ->middleware(['web']);