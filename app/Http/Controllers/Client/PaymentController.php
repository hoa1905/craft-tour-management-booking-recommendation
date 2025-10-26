<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{

    private function validateBookingRequest(Request $request)
    {
        // Đây là tất cả các trường bạn gửi lên từ form
        return Validator::make($request->all(), [
            'tour_id' => 'required|integer|exists:tours,tour_id',
            'schedule_id' => 'nullable|integer|exists:tour_schedules,schedule_id',
            'adult_quantity' => 'required|integer|min:1',
            'child_quantity' => 'required|integer|min:0',
            'price_adult_at_booking' => 'required|numeric',
            'price_child_at_booking' => 'required|numeric',
            'total_price' => 'required|numeric|min:0',
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:150',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:office,paypal,momo',
        ]);
    }

    /**
     * Hàm private để tạo Booking và Payment trong một Transaction
     */
    private function createBookingAndPayment(array $bookingData, string $paymentMethod, string $bookingStatus, ?array $paymentResponse = null)
    {
        // DB::transaction sẽ tự động commit nếu thành công,
        // hoặc rollback nếu có lỗi (exception)
        return DB::transaction(function () use ($bookingData, $paymentMethod, $bookingStatus, $paymentResponse) {
            // 1. Tạo Booking
            $booking = Booking::create([
                'user_id' => Auth::guard('client')->id(),
                'tour_id' => $bookingData['tour_id'],
                'schedule_id' => $bookingData['schedule_id'],
                'full_name' => $bookingData['full_name'],
                'phone_number' => $bookingData['phone'], // Map 'phone' từ form
                'email' => $bookingData['email'],
                'address' => $bookingData['address'],
                'adult_quantity' => $bookingData['adult_quantity'], // Map 'adult_quantity' từ form
                'child_quantity' => $bookingData['child_quantity'], // Map 'child_quantity'
                'price_adult_at_booking' => $bookingData['price_adult_at_booking'], // MỚI
                'price_child_at_booking' => $bookingData['price_child_at_booking'], // MỚI
                'total_price' => $bookingData['total_price'], // Tiền VND
                'status' => $bookingStatus, // 'pending' hoặc 'paid'
            ]);

            // 2. Tạo Payment
            $payment = new Payment();
            $payment->booking_id = $booking->booking_id;
            $payment->payment_method = $paymentMethod;

            if ($paymentMethod === 'PayPal' && $paymentResponse) {
                // Xử lý thanh toán bằng PayPal
                $payment->transaction_id = $paymentResponse['id']; // PayPal transaction ID
                $payment->amount = $paymentResponse['amount']; // Số tiền USD
                $payment->payper_name = $paymentResponse['payer_name'];
                $payment->payper_email = $paymentResponse['payer_email'];
                $payment->status = 'success';
            } else if ($paymentMethod === 'Office') {
                // Xử lý thanh toán tại văn phòng

            } else if ($paymentMethod === 'MoMo') {
                // Xử lý khi thanh toán bằng MoMo

            }

            $payment->save();

            // Trả về booking đã tạo
            return $booking;
        });
    }

    /**
     * Bắt đầu quá trình thanh toán PayPal
     */
    public function paypal(Request $request)
    {
        $validator = $this->validateBookingRequest($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // 1. Quy đổi tiền (VND sang USD)
        $vnd_total = (float) $request->total_price;
        $vnd_to_usd_rate = config('paypal.vnd_to_usd_rate', 26310); // Lấy từ config, 26310 là dự phòng
        $usd_total = round($vnd_total / $vnd_to_usd_rate, 2);

        // 2. Lưu tất cả dữ liệu form vào session để dùng sau khi thanh toán thành công
        session(['paypal_booking_data' => $request->all()]);

        // 3. Tạo đơn hàng PayPal
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.paypalSuccess'),
                "cancel_url" => route('payment.paypalCancel'),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $usd_total
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return response()->json(['success' => true, 'redirect_url' => $link['href']]);
                }
            }
        }
        session()->forget('paypal_booking_data');
        return response()->json(['success' => false, 'message' => $response['message'] ?? 'Không thể tạo đơn hàng PayPal.']);
    }


    public function paypalSuccess(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);
        // 1. Lấy dữ liệu booking đã lưu tạm từ session
        $bookingData = session('paypal_booking_data');
        if (!$bookingData) {
            return redirect()->route('booking')->with('error', 'Phiên đặt tour đã hết hạn. Vui lòng thử lại.');
        }
        // 2. Kiểm tra thanh toán
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            // 3. Thanh toán thành công -> Bắt đầu lưu CSDL
            try {
                // Chuẩn bị dữ liệu payment
                $paymentResponseData = [
                    'id' => $response['id'],
                    'amount' => $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                    'payer_name' => $response['payer']['name']['given_name'] . ' ' . $response['payer']['name']['surname'],
                    'payer_email' => $response['payer']['email_address'],
                ];

                // Gọi hàm private để tạo Booking và Payment
                $this->createBookingAndPayment($bookingData, 'PayPal', 'paid', $paymentResponseData);

                // Xóa session
                session()->forget('paypal_booking_data');
                session()->forget('booking_data'); // Xóa luôn session giỏ hàng gốc

                // Chuyển hướng đến trang thành công
                return redirect()->route('account.myBookings')->with('success', 'Đặt tour và thanh toán thành công!');
            } catch (\Exception $e) {
                // Nếu lỗi CSDL (đã được auto-rollback)
                Log::error('PayPal Success DB Error: ' . $e->getMessage());
                // Xem xét việc hoàn tiền PayPal ở đây nếu cần
                return redirect()->route('booking')->with('error', 'Lỗi lưu dữ liệu. Thanh toán của bạn có thể sẽ được hoàn lại.');
            }
        }

        // Thanh toán không thành công
        session()->forget('paypal_booking_data');
        return redirect()->route('booking')->with('error', 'Thanh toán PayPal không thành công. Vui lòng thử lại.');
    }
    /**
     * Xử lý khi người dùng hủy thanh toán PayPal
     */
    public function paypalCancel(Request $request)
    {
        session()->forget('paypal_booking_data');
        return redirect()->route('booking')->with('error', 'Bạn đã hủy thanh toán PayPal.');
    }
}
