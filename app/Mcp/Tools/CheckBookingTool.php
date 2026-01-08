<?php

namespace App\Mcp\Tools;

use App\Models\Booking;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CheckBookingTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $name = 'check_booking';
    protected string $description = 'Kiểm tra trạng thái đơn đặt tour mới nhất của khách hàng.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
       // Lấy user đang đăng nhập thông qua cơ chế Auth của MCP/Laravel
       $user = Auth::guard('client')->user();

        if (!$user) {
            return Response::error('Bạn cần đăng nhập để kiểm tra đơn hàng.');
        }
        
        $booking = Booking::where('user_id', $user->id)
            ->with('tour:tour_id,tour_name')
            ->latest()
            ->first();

        if (!$booking) {
            return Response::text("Bạn chưa có đơn đặt tour nào.");
        }

        return Response::text("Đơn hàng mới nhất: Tour {$booking->tour->tour_name}, Trạng thái: {$booking->status}");
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema($schema): array
    {
        return []; // Không cần tham số, lấy user từ context
    }
}
