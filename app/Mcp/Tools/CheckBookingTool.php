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
       $user = Auth::guard('client')->user();
        if (!$user) {
            return Response::error('Bạn cần đăng nhập để kiểm tra đơn hàng.');
        }

        $validated = $request->validate([
            'status' => 'nullable|string'
        ]);
        
        // Input từ AI
        $rawStatus = isset($validated['status']) ? mb_strtolower($validated['status'], 'UTF-8') : null;
        $dbStatus = null;

        // MAPPING TRẠNG THÁI
        if ($rawStatus) {
            // Bảng map từ khóa tiếng Việt/Anh sang giá trị chuẩn trong Database
            $mapping = [
                // Fix lỗi: AI hay trả về từ này (2 chữ l)
                'đã huỷ' => 'canceled',
                'đã thanh toán' => 'paid',
                'pending' => 'waiting_payment',
                'chờ thanh toán' => 'waiting_payment',
                'đã hoàn thành' => 'completed',
                'hoạt động' => 'active',
            ];

            // Kiểm tra xem từ khóa AI gửi có nằm trong bảng map không
            // Nếu có thì lấy giá trị chuẩn, nếu không thì giữ nguyên
            $dbStatus = $mapping[$rawStatus] ?? $rawStatus;
        }
        // ---------------------------------------------

        $query = Booking::where('user_id', $user->user_id)
            ->with('tour:tour_id,tour_name');

        if ($dbStatus) {
            $query->where('status', $dbStatus);
        }

        $bookings = $query->latest()->limit(5)->get();

        if ($bookings->isEmpty()) {
            $msg = $dbStatus 
                ? "Không tìm thấy đơn hàng nào có trạng thái '{$rawStatus}' (Hệ thống tìm: {$dbStatus})." 
                : "Bạn chưa có đơn đặt tour nào.";
            return Response::text($msg);
        }

        $result = $bookings->map(function ($booking) {
            // Map ngược lại tiếng Việt để hiển thị cho đẹp
            $statusLabel = match($booking->status) {
                'waiting_payment' => 'Chờ thanh toán',
                'paid' => 'Đã thanh toán',
                'active' => 'Đang hoạt động',
                'canceled' => 'Đã hủy',
                'completed' => 'Hoàn thành',
                default => $booking->status
            };

            return [
                'tour_name' => $booking->tour->tour_name,
                'status_text' => $statusLabel,     // Chữ hiển thị
                'date' => $booking->created_at->format('d/m/Y'),
                'link' => route('account.myBookings.detail', ['booking' => $booking->booking_id]),
            ];
        });

        return Response::text("Danh sách đơn hàng: " . $result->toJson());
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema($schema): array
    {
        return [
            'status' => $schema->string()
                ->description('Trạng thái đơn hàng. Các giá trị hợp lệ trong DB: waiting_payment, paid, active, canceled, completed. Nếu khách nói "hủy", hãy ưu tiên gửi "canceled".'),
        ];
    }
}
