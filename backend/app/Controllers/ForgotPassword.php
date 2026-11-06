<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class ForgotPassword extends BaseController
{
    use ResponseTrait;

    public function requestOtp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $identifier = trim($data['identifier'] ?? '');

        if (!$identifier) {
            return $this->respond(['success' => false, 'message' => 'Please enter your email'], 400);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $identifier)->first();

        if (!$user) {
            return $this->respond(['success' => false, 'message' => 'No account found with this email'], 404);
        }

        // Generate 6-digit OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $userModel->update($user->user_id, [
            'reset_otp' => $otp,
            'reset_otp_expires' => $expires,
            'otp_attempts' => 0, 
            'otp_lockout_until' => null
        ]);

        // SEND THE EMAIL
        $email = \Config\Services::email();
        $email->setTo($user->email);
        $email->setSubject('Password Reset OTP - ATBI Intertrade System');
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                <h2 style='color: #047857;'>Password Reset Request</h2>
                <p>Hello <strong>{$user->first_name} {$user->last_name}</strong>,</p>
                <p>You requested to reset your password. Use the code below to proceed:</p>
                <div style='font-size: 32px; font-weight: bold; color: #0d9488; padding: 15px; background: #f0fdf4; text-align: center; letter-spacing: 5px; border-radius: 10px;'>
                    {$otp}
                </div>
                <p>This OTP will expire in <strong>10 minutes</strong>.</p>
                <p>If you did not request this, please ignore this email.</p>
            </div>
        </body>
        </html>
        ";

        $email->setMessage($message);
        $email->setMailType('html');

        if ($email->send()) {
            return $this->respond([
                'success' => true,
                'message' => 'OTP sent to your email',
                'user_id' => $user->user_id
            ]);
        } else {
            log_message('error', 'Failed to send OTP email');
            return $this->respond(['success' => false, 'message' => 'Failed to send email. Please check your SMTP settings.'], 500);
        }
    }

        public function verifyOtp()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = (int)($data['user_id'] ?? 0);
        $otp = trim($data['otp'] ?? '');

        if (!$userId || !$otp) {
            return $this->respond(['success' => false, 'message' => 'Missing fields'], 400);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->respond(['success' => false, 'message' => 'User not found'], 404);
        }

        if (strtotime($user->reset_otp_expires) < time()) {
            return $this->respond(['success' => false, 'message' => 'OTP has expired. Please request a new one.'], 400);
        }

        if ($user->reset_otp !== $otp) {
            return $this->respond(['success' => false, 'message' => 'Invalid OTP. Please try again.'], 400);
        }

        return $this->respond(['success' => true, 'message' => 'OTP verified']);
    }

            public function resetPassword()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = (int)($data['user_id'] ?? 0);
        $newPassword = $data['new_password'] ?? '';

        if (!$userId || !$newPassword) {
            return $this->respond(['success' => false, 'message' => 'Missing fields'], 400);
        }

        if (strlen($newPassword) < 8) {
            return $this->respond(['success' => false, 'message' => 'Password must be 8+ characters'], 400);
        }

        $userModel = new UserModel();
        
        // Hash the password manually here because we removed the auto-hash on update
        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_otp' => null,
            'reset_otp_expires' => null,
            'otp_attempts' => 0,
            'otp_lockout_until' => null
        ]);

        return $this->respond(['success' => true, 'message' => 'Password reset successfully']);
    }
}