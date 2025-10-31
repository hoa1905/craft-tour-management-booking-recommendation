<?php

return [
  // Url thanh toán
  'vnp_Url' => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

  // Mã Terminal (Mã Website)
  'vnp_TmnCode' => env('VNP_TMN_CODE', ''),

  // Chuỗi bí mật
  'vnp_HashSecret' => env('VNP_HASH_SECRET', ''),

  // Url trả về sau khi thanh toán (cho trình duyệt user)
  'vnp_ReturnUrl' => env('VNP_RETURN_URL', ''),

  // Url IPN (VNPAY Server gọi để báo kết quả)
  'vnp_IpnUrl' => env('VNP_IPN_URL', ''),
];
