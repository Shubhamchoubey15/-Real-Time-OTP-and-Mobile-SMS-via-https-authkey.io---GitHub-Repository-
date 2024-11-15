<?php
session_start();
header('Content-Type: application/json'); // Ensure JSON content type

// Function to validate phone number format
function validatePhoneNumber($phone) {
    return preg_match('/^[0-9]{10}$/', $phone); // Validate 10-digit number
}

// Get POST data (JSON format)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if phone number or OTP is provided
if (isset($data['phone'])) {
    $phone = $data['phone'];

    // Validate phone number
    if (!validatePhoneNumber($phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit;
    }

    // Generate OTP
    $otp = rand(1000, 9999);
    $_SESSION['otp'] = $otp;
    $_SESSION['phone'] = $phone;

    // API details
    $apiKey = "";
    $countryCode = "91";
    $sid = "";
    $companyName = "";
    $apiUrl = "https://console.authkey.io/restapi/requestjson.php";

    // Request data for API
    $requestData = [
        "country_code" => $countryCode,
        "mobile" => $phone,
        "sid" => $sid,
        "otp" => $otp,
        "company" => $companyName,
    ];

    // cURL request
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic ' . $apiKey,
        ],
    ]);

    $response = curl_exec($curl);

    // Check for cURL errors
    if (curl_errno($curl)) {
        echo json_encode(['success' => false, 'message' => 'Error in API: ' . curl_error($curl)]);
        curl_close($curl);
        exit;
    }

    curl_close($curl);

    // Decode API response
    $responseData = json_decode($response, true);

    // Validate JSON response
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid API response format']);
        exit;
    }

    // Handle API response
    if (isset($responseData['type']) && $responseData['type'] === "success") {
        // Success: OTP sent correctly
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully', 'otp' => $otp]);
    } else {
        // Failure: Do not show any error message in case of failure
        // Optionally, you could log the failure for debugging
        echo json_encode(['success' => true, 'message' => 'OTP request failed, no error shown']);
    }
} elseif (isset($data['otp'])) {
    // Verify OTP when received for verification
    $enteredOTP = $data['otp'];

    if ($enteredOTP == $_SESSION['otp']) {
        echo json_encode(['success' => true, 'message' => 'OTP Verified!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phone number or OTP is missing']);
}
?>
