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
            return $this->respond(['success' => false, 'message' => 'Please enter your email or username'], 400);
        }

        $userModel = new UserModel();
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $userModel->where('email', $identifier)->first()
            : $userModel->where('username', $identifier)->first();

        if (!$user) {
            return $this->respond(['success' => false, 'message' => 'No account found'], 404);
        }

        // Generate 6-digit OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $userModel->update($user->user_id, [
            'reset_otp' => $otp,
            'reset_otp_expires' => $expires
        ]);

        // In production, send via email. For testing, return in response:
        log_message('info', "OTP for {$user->email}: {$otp}");

        return $this->respond([
            'success' => true,
            'message' => 'OTP sent to your email',
            'user_id' => $user->user_id,
            'debug_otp' => $otp  // ⚠️ Remove this in production!
        ]);
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

        if (!$user || $user->reset_otp !== $otp) {
            return $this->respond(['success' => false, 'message' => 'Invalid OTP'], 400);
        }

        if (strtotime($user->reset_otp_expires) < time()) {
            return $this->respond(['success' => false, 'message' => 'OTP has expired'], 400);
        }

        return $this->respond(['success' => true, 'message' => 'OTP verified', 'user_id' => $userId]);
    }

    public function resetPassword()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = (int)($data['user_id'] ?? 0);
        $otp = trim($data['otp'] ?? '');
        $newPassword = $data['new_password'] ?? '';

        if (!$userId || !$otp || !$newPassword) {
            return $this->respond(['success' => false, 'message' => 'Missing fields'], 400);
        }

        if (strlen($newPassword) < 6) {
            return $this->respond(['success' => false, 'message' => 'Password must be 6+ characters'], 400);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user || $user->reset_otp !== $otp) {
            return $this->respond(['success' => false, 'message' => 'Invalid OTP'], 400);
        }

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_otp' => null,
            'reset_otp_expires' => null
        ]);

        return $this->respond(['success' => true, 'message' => 'Password reset successfully']);
    }
}