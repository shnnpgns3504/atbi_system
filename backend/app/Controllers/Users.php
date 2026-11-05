<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{
    use ResponseTrait;

    private function requireAdmin()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $requesterId = (int)($data['requester_id'] ?? $this->request->getGet('requester_id') ?? 0);
        
        if (!$requesterId) return null;

        $userModel = new UserModel();
        $requester = $userModel->find($requesterId);

        if (!$requester || $requester->role !== 'admin') return null;

        return $requester;
    }

    public function index()
    {
        if (!$this->requireAdmin()) {
            return $this->respond(['success' => false, 'message' => 'Admin access required'], 403);
        }

        $userModel = new UserModel();
        $users = $userModel->select('user_id, username, email, full_name, role, is_active, status, atbi_affiliation, phone_number, created_at')
                          ->orderBy('status', 'ASC')
                          ->orderBy('created_at', 'DESC')
                          ->findAll();

        return $this->respond(['success' => true, 'data' => $users]);
    }

    public function approveUser()
    {
        if (!$this->requireAdmin()) {
            return $this->respond(['success' => false, 'message' => 'Admin access required'], 403);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userModel = new UserModel();
        
        // 1. Update status to approved
        $userModel->update($data['user_id'], ['status' => 'approved']);

        // 2. Fetch the user to get their email
        $user = $userModel->find($data['user_id']);

        // 3. Send the approval email
        if ($user && !empty($user->email)) {
            $email = \Config\Services::email();
            $email->setTo($user->email);
            $email->setSubject('ATBI Intertrade System - Account Approved');
            
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <h2 style='color: #047857;'>Account Approved!</h2>
                    <p>Hello <strong>{$user->first_name} {$user->last_name}</strong>,</p>
                    <p>Your registration for the ATBI Intertrade System has been successfully approved by the administrator.</p>
                    <p>You can now log in using your registered username and password.</p>
                    <br>
                    <p>Thank you,<br>ATBI Administration</p>
                </div>
            </body>
            </html>
            ";

            $email->setMessage($message);
            $email->setMailType('html');
            
            if (!$email->send()) {
                log_message('error', 'Failed to send approval email to: ' . $user->email);
            }
        }

        return $this->respond(['success' => true, 'message' => 'User approved successfully and email sent.']);
    }

    public function rejectUser()
    {
        if (!$this->requireAdmin()) {
            return $this->respond(['success' => false, 'message' => 'Admin access required'], 403);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userModel = new UserModel();
        $userModel->update($data['user_id'], ['status' => 'rejected']);

        return $this->respond(['success' => true, 'message' => 'User rejected successfully']);
    }

    public function assignAdmin()
    {
        if (!$this->requireAdmin()) return $this->respond(['success' => false, 'message' => 'Admin access required'], 403);

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $userModel = new UserModel();
        $target = $userModel->find($data['user_id']);

        if ($target->user_id === $data['requester_id']) {
            return $this->respond(['success' => false, 'message' => 'Cannot change your own role'], 400);
        }

        $userModel->update($data['user_id'], ['role' => $data['role']]);
        return $this->respond(['success' => true, 'message' => "Role updated to {$data['role']}"]);
    }
}