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
            'payment_method' => 'required|string|in:office,paypal,vnpay',
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
                'phone_number' => $bookingData['phone'],
                'email' => $bookingData['email'],
                'address' => $bookingData['address'],
                'adult_quantity' => $bookingData['adult_quantity'],
                'child_quantity' => $bookingData['child_quantity'],
                'price_adult_at_booking' => $bookingData['price_adult_at_booking'],
                'price_child_at_booking' => $bookingData['price_child_at_booking'],
                'total_price' => $bookingData['total_price'], // Tiền VND
                'status' => $bookingStatus, // 'waiting_payment' hoặc 'paid'
            ]);

            // 2. Tạo Payment
            $payment = new Payment();
            $payment->booking_id = $booking->booking_id;
            $payment->payment_method = $paymentMethod;

            if ($paymentMethod === 'Office') {
                // Xử lý thanh toán tại văn phòng
                $payment->transaction_id = 'OFFICE_' . $booking->booking_id . '_' . time();
                $payment->amount = $bookingData['total_price']; // Số tiền VND
                $payment->payper_name = $bookingData['full_name'];
                $payment->payper_email = $bookingData['email'];
                $payment->status = 'waiting_payment';
            } else if ($paymentMethod === 'PayPal' && $paymentResponse) {
                // Xử lý thanh toán bằng PayPal
                $payment->transaction_id = $paymentResponse['id']; // PayPal transaction ID
                $payment->amount = $paymentResponse['amount']; // Số tiền USD
                $payment->payper_name = $paymentResponse['payer_name'];
                $payment->payper_email = $paymentResponse['payer_email'];
                $payment->status = 'success';
            } else if ($paymentMethod === 'VNPAY') {
                // Đây là lúc tạo đơn waiting_payment, chưa có giao dịch
                $payment->transaction_id = 'VNPAY_' . $booking->booking_id . '_' . time(); // ID tạm
                $payment->amount = $bookingData['total_price']; // Lưu tiền VND
                $payment->payper_name = $bookingData['full_name'];
                $payment->payper_email = $bookingData['email'];
                $payment->status = 'waiting_payment';
            }

            $payment->save();

            // Trả về booking đã tạo
            return $booking;
        });
    }

    /**
     * Thanh toán tại văn phòng
     */
    public function officePayment(Request $request)
    {
        $validator = $this->validateBookingRequest($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        try {
            // Gọi hàm private để tạo Booking và Payment
            // Trạng thái booking là 'waiting_payment' (chờ thanh toán)
            $this->createBookingAndPayment($request->all(), 'Office', 'waiting_payment', null);

            // Xóa session giỏ hàng gốc
            session()->forget('booking_data');

            $message = 'Đặt tour thành công! Vui lòng đến văn phòng để xác nhận và thanh toán.';

            // Gửi message vào session (dự phòng nếu trang sau cần)
            session()->flash('success', $message);

            // Trả về JSON thành công cho JavaScript
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('account.myBookings')
            ]);
        } catch (\Exception $e) {
            Log::error('Office Payment DB Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ. Không thể đặt tour.']);
        }
    }

    /**
     * Thanh toán bằng PayPal
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
                return redirect()->route('account.myBookings')->with('success', 'Đặt tour và thanh toán thành công bằng PayPal!');
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


    /**
     * Thanh toán bằng VNPAY
     */
    public function vnpayPayment(Request $request)
    {
        $validator = $this->validateBookingRequest($request);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // 1. Tạo đơn hàng 'waiting_payment' TẠM THỜI trong CSDL
        try {
            $booking = $this->createBookingAndPayment($request->all(), 'VNPAY', 'waiting_payment', null);
        } catch (\Exception $e) {
            Log::error('VNPAY Payment DB Error (Pre-create): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ khi tạo đơn hàng.']);
        }

        // 2. Chuẩn bị dữ liệu gửi sang VNPAY
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = config('vnpay.vnp_ReturnUrl');
        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');

        $vnp_TxnRef = (string) $booking->booking_id; // Mã đơn hàng của bạn (trường vnp_TxnRef phải là chuỗi)
        $vnp_OrderInfo = "Thanh toan don hang tour " . $booking->booking_id;
        $vnp_OrderType = 'other';
        $vnp_Amount = $booking->total_price * 100; // VNPAY yêu cầu nhân 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = ''; // Trống để VNPAY hiển thị cổng chọn ngân hàng
        $vnp_IpAddr = $request->ip();
        $vnp_CreateDate = date('YmdHis');

        // BƯỚC 1: GHI LOG BIẾN BỊ NGHI NGỜ
        Log::info('VNPAY DEBUG - IP Address:', ['ip' => $vnp_IpAddr]);
        Log::info('VNPAY DEBUG - Amount:', ['amount' => $vnp_Amount]);
        // ================================================

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];
        // BƯỚC 2: GHI LOG TOÀN BỘ DỮ LIỆU SẮP GỬI
        Log::info('VNPAY DEBUG - Input Data:', $inputData);
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return response()->json(['success' => true, 'redirect_url' => $vnp_Url]);
    }

    /**
     * Xử lý khi VNPAY trả về (Return URL)
     * Chỉ dùng để hiển thị thông báo cho user
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Chỉ hiển thị, không cập nhật DB ở đây
                return redirect()->route('account.myBookings')
                    ->with('success', 'Giao dịch VNPAY thành công! Đang chờ xác nhận cuối cùng...');
            } else {
                return redirect()->route('booking')
                    ->with('error', 'Giao dịch VNPAY không thành công. Mã lỗi: ' . $request->vnp_ResponseCode);
            }
        } else {
            return redirect()->route('booking')
                ->with('error', 'Lỗi chữ ký VNPAY. Giao dịch không hợp lệ.');
        }
    }

    /**
     * Xử lý IPN do VNPAY Server gọi
     * Đây là nơi duy nhất cập nhật CSDL
     */
    public function vnpayNotify(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        Log::info('VNPAY IPN Request:', $inputData);

        try {
            // 1. Kiểm tra chữ ký
            if ($secureHash != $vnp_SecureHash) {
                Log::warning('VNPAY IPN: Invalid Signature', $inputData);
                return response()->json(["RspCode" => "97", "Message" => "Invalid Signature"]);
            }

            // 2. Lấy Booking và Payment
            $booking_id = $inputData['vnp_TxnRef'];
            $booking = Booking::find($booking_id);
            $payment = Payment::where('booking_id', $booking_id)->first();

            if (!$booking || !$payment) {
                Log::error('VNPAY IPN: Booking or Payment not found', $inputData);
                return response()->json(["RspCode" => "01", "Message" => "Order not found"]);
            }

            // 3. Kiểm tra trạng thái Payment (tránh IPN gọi nhiều lần)
            if ($payment->status != 'waiting_payment') {
                Log::info('VNPAY IPN: Order already processed', $inputData);
                return response()->json(["RspCode" => "02", "Message" => "Order already processed"]);
            }

            // 4. Kiểm tra mã giao dịch và số tiền
            if ($inputData['vnp_Amount'] != ($booking->total_price * 100)) {
                Log::error('VNPAY IPN: Invalid Amount', $inputData);
                return response()->json(["RspCode" => "04", "Message" => "Invalid Amount"]);
            }

            // 5. Cập nhật CSDL
            DB::beginTransaction();
            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                // Giao dịch thành công
                $booking->status = 'paid';
                $payment->status = 'success';
                $payment->transaction_id = $inputData['vnp_TransactionNo']; // Mã GD của VNPAY
            } else {
                // Giao dịch thất bại
                $booking->status = 'canceled';
                $payment->status = 'failed';
                $payment->transaction_id = $inputData['vnp_TransactionNo'] ?? $payment->transaction_id;
            }
            $booking->save();
            $payment->save();
            DB::commit();

            Log::info('VNPAY IPN: Successfully processed', $inputData);
            return response()->json(["RspCode" => "00", "Message" => "Confirm Success"]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VNPAY IPN: DB Update Error', ['error' => $e->getMessage(), 'data' => $inputData]);
            return response()->json(["RspCode" => "99", "Message" => "Server Error"]);
        }
    }
}
