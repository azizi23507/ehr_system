<?php
// Gmail SMTP Email Service - Secure Version
// Credentials loaded from .env file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        $this->loadEnv();
        
        $this->smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->smtp_port = getenv('SMTP_PORT') ?: 587;
        $this->smtp_username = getenv('SMTP_USERNAME');
        $this->smtp_password = getenv('SMTP_PASSWORD');
        $this->from_email = getenv('SMTP_FROM_EMAIL');
        $this->from_name = getenv('SMTP_FROM_NAME') ?: 'EHR System';
    }
    
    private function loadEnv() {
        $env_file = __DIR__ . '/../.env';
        if (!file_exists($env_file)) {
            error_log('ERROR: .env file not found at ' . $env_file);
            return;
        }
        
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                putenv(trim($name) . '=' . trim($value));
            }
        }
    }
    
    private function sendEmail($to_email, $subject, $html_body) {
        if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
            error_log("INVALID EMAIL: $to_email");
            return false;
        }
        
        // Check if vendor/autoload.php exists
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (!file_exists($autoload)) {
            error_log('ERROR: PHPMailer not installed. Run: composer require phpmailer/phpmailer');
            return false;
        }
        
        require_once $autoload;
        
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_username;
            $mail->Password = $this->smtp_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // FIXED: Use constant
            $mail->Port = $this->smtp_port;
            $mail->CharSet = 'UTF-8';
            
            // Disable SSL verification for localhost (WAMP)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to_email);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_body;
            
            $mail->send();
            error_log("EMAIL SENT to: $to_email");
            return true;
            
        } catch (Exception $e) {
            error_log("EMAIL ERROR: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    public function sendPasswordResetEmail($to_email, $doctor_name, $reset_token) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_path = dirname(dirname($_SERVER['PHP_SELF']));
        if ($base_path === '/' || $base_path === '\\') $base_path = '';
        
        $reset_link = $protocol . "://" . $host . $base_path . "/auth/reset_password.php?token=" . urlencode($reset_token);
        $subject = "Password Reset Request - EHR System";
        
        $html_body = '<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="background:#0d6efd;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;">Password Reset</h1></td></tr>
<tr><td style="padding:40px 30px;">
<p style="font-size:16px;color:#333;margin:0 0 20px;">Dear Dr. ' . htmlspecialchars($doctor_name) . ',</p>
<p style="font-size:16px;color:#333;margin:0 0 30px;">Click below to reset your password:</p>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center" style="padding:0 0 30px;">
<a href="' . $reset_link . '" style="display:inline-block;padding:15px 40px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold;">Reset Password</a>
</td></tr></table>
<p style="font-size:14px;color:#666;margin:0 0 10px;">Or copy this link:</p>
<p style="font-size:14px;color:#0d6efd;word-break:break-all;background:#f8f9fa;padding:15px;margin:0 0 30px;">' . $reset_link . '</p>
<p style="font-size:16px;color:#dc3545;margin:0 0 20px;"><strong>Expires in 1 hour</strong></p>
<p style="font-size:16px;color:#333;margin:0;">Best regards,<br><strong>EHR System</strong></p>
</td></tr>
<tr><td style="background:#f8f9fa;padding:20px;text-align:center;">
<p style="font-size:12px;color:#6c757d;margin:0;">&copy; 2026 EHR System</p>
</td></tr></table></td></tr></table></body></html>';
        
        return $this->sendEmail($to_email, $subject, $html_body);
    }
    
    public function sendVerificationEmail($to_email, $doctor_name, $verification_token) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_path = dirname(dirname($_SERVER['PHP_SELF']));
        if ($base_path === '/' || $base_path === '\\') $base_path = '';
        
        $verification_link = $protocol . "://" . $host . $base_path . "/auth/verify_email.php?token=" . urlencode($verification_token);
        $subject = "Verify Your Email - EHR System";
        
        $html_body = '<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="background:#198754;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;">Verify Email</h1></td></tr>
<tr><td style="padding:40px 30px;">
<p style="font-size:16px;color:#333;margin:0 0 20px;">Dear Dr. ' . htmlspecialchars($doctor_name) . ',</p>
<p style="font-size:16px;color:#333;margin:0 0 30px;">Please verify your email:</p>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center" style="padding:0 0 30px;">
<a href="' . $verification_link . '" style="display:inline-block;padding:15px 40px;background:#198754;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold;">Verify Email</a>
</td></tr></table>
<p style="font-size:14px;color:#666;margin:0 0 10px;">Or copy this link:</p>
<p style="font-size:14px;color:#198754;word-break:break-all;background:#f8f9fa;padding:15px;margin:0 0 30px;">' . $verification_link . '</p>
<p style="font-size:16px;color:#333;margin:0;">Best regards,<br><strong>EHR System</strong></p>
</td></tr>
<tr><td style="background:#f8f9fa;padding:20px;text-align:center;">
<p style="font-size:12px;color:#6c757d;margin:0;">&copy; 2026 EHR System</p>
</td></tr></table></td></tr></table></body></html>';
        
        return $this->sendEmail($to_email, $subject, $html_body);
    }
    
    public function sendRegistrationConfirmation($to_email, $doctor_name) {
        $subject = "Welcome to EHR System";
        
        $html_body = '<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="background:#198754;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;">Welcome!</h1></td></tr>
<tr><td style="padding:40px 30px;">
<p style="font-size:16px;color:#333;margin:0 0 20px;">Dear Dr. ' . htmlspecialchars($doctor_name) . ',</p>
<p style="font-size:16px;color:#333;margin:0 0 20px;">Your account has been created successfully!</p>
<p style="font-size:16px;color:#333;margin:0;">Best regards,<br><strong>EHR System</strong></p>
</td></tr>
<tr><td style="background:#f8f9fa;padding:20px;text-align:center;">
<p style="font-size:12px;color:#6c757d;margin:0;">&copy; 2026 EHR System</p>
</td></tr></table></td></tr></table></body></html>';
        
        return $this->sendEmail($to_email, $subject, $html_body);
    }
}
?>
