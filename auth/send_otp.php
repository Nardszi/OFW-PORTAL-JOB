<?php
session_start();

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// require '../vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    
    // Store in session for verification
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;

    // --- OPTION 1: BREVO API (Recommended) ---
    // Use this method if SMTP is blocked. Requires a Brevo API Key from brevo.com
    $apiKey = 'YOUR_BREVO_API_KEY'; // TODO: Replace with your actual Brevo API Key
    $url = 'https://api.brevo.com/v3/smtp/email';

    $data = [
        'sender' => ['name' => 'OFW Management', 'email' => 'ralphbelandres1@gmail.com'],
        'to' => [['email' => $email]],
        'subject' => 'Your Verification Code',
        'htmlContent' => "<html><body><h3>Your OTP is: <strong>$otp</strong></h3><p>Use this code to complete your registration.</p></body></html>"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for local XAMPP

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201 || $httpCode == 200) {
        echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully via Brevo API.']);
    } else {
        // --- OPTION 2: GOOGLE APP PASSWORD (Gmail SMTP) ---
        // Uncomment below to use Gmail SMTP instead of Brevo API
        /*
        require '../vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ralphbelandres1@gmail.com';
            $mail->Password   = 'YOUR_GOOGLE_APP_PASSWORD'; // Generate at myaccount.google.com/apppasswords
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('ralphbelandres1@gmail.com', 'OFW Management');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
            $mail->Body    = "<html><body><h3>Your OTP is: <strong>$otp</strong></h3><p>Use this code to complete your registration.</p></body></html>";
            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'OTP sent via Gmail SMTP.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
        }
        */
        
        if ($curlError) {
            echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $curlError]);
        } else {
            $respData = json_decode($response, true);
            $apiMsg = isset($respData['message']) ? $respData['message'] : 'Unknown error from Brevo';
            echo json_encode(['status' => 'error', 'message' => 'Brevo API Error: ' . $apiMsg]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>