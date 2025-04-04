<?php
// Izinkan akses dari server lain (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Jika metode POST, proses permintaan (fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Baca data JSON dari body
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    // Pastikan phone dan uuid ada
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    $uuid  = isset($input['uuid']) ? trim($input['uuid']) : '';
    // otp bersifat opsional
    $otp   = isset($input['otp']) ? trim($input['otp']) : '';

    if (empty($phone) || empty($uuid)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'phone dan uuid wajib disediakan.'
        ]);
        exit;
    }

    // Siapkan payload: jika otp ada, sertakan; jika tidak, hanya phone dan uuid.
    $payload = ($otp === '') 
              ? ['phone' => $phone, 'uuid' => $uuid]
              : ['phone' => $phone, 'otp' => $otp, 'uuid' => $uuid];

    // Inisialisasi CURL untuk mengirim POST ke API https://apii.biz.id/api/otp
    $ch = curl_init('https://apii.biz.id/api/otp');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        echo json_encode([
            'status' => 'error',
            'message' => $error_msg
        ]);
        exit;
    }
    curl_close($ch);
    echo $result;
    exit;
}
?>
