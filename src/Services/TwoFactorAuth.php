<?php
declare(strict_types=1);

namespace App\Services;

use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use Exception;

class TwoFactorAuth {
    private string $appName = 'PHP 2FA By Cursor';
    
    // Generate a secure secret for 2FA
    public function generateSecret(): string {
        try {
            // Generate a random 20-byte (160-bit) secret
            $secret = random_bytes(20);
            // Convert it to base32 for better readability and compatibility
            return Base32::encodeUpper($secret);
        } catch (Exception $e) {
            error_log('Failed to generate 2FA secret: ' . $e->getMessage());
            throw new Exception('Failed to generate 2FA secret');
        }
    }
    
    // Verify a 2FA code against the secret
    public function verifyCode(string $secret, string $code): bool {
        try {
            error_log("Verifying code: $code with secret: $secret");
            
            // Clean the code by removing non-numeric characters
            $code = preg_replace('/[^0-9]/', '', $code);
            
            // Ensure the code is exactly 6 digits
            if (strlen($code) !== 6) {
                error_log("Invalid code length: " . strlen($code));
                return false;
            }
            
            // Create TOTP object with the secret
            $totp = TOTP::create($secret);
            
            // Verify the code with current timestamp
            $result = $totp->verify($code);
            error_log("Verification result: " . ($result ? "success" : "failed"));
            return $result;
        } catch (Exception $e) {
            error_log('Failed to verify 2FA code: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getQRCodeUrl(string $email, string $secret): string {
        try {
            // Create TOTP object
            $totp = TOTP::create(
                $secret,
                30,    // 30-second period
                'sha1', // Algorithm
                6       // 6 digits
            );
            
            // Set the label and issuer
            $totp->setLabel($email);
            $totp->setIssuer($this->appName);
            
            // Get the provisioning URI
            $otpauthUrl = $totp->getProvisioningUri();
            error_log("Generated otpauth URL: $otpauthUrl");
            
            // Use QR Server API to generate QR code
            $qrUrl = sprintf(
                'https://api.qrserver.com/v1/create-qr-code/?%s',
                http_build_query([
                    'size' => '300x300',
                    'data' => $otpauthUrl,
                    'margin' => '10',
                    'format' => 'png'
                ])
            );
            
            error_log("Generated QR code URL: $qrUrl");
            return $qrUrl;
        } catch (Exception $e) {
            error_log('Failed to generate QR code: ' . $e->getMessage());
            throw new Exception('Failed to generate QR code');
        }
    }
} 