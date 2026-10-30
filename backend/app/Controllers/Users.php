<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Users extends BaseController
{
    use ResponseTrait;

    private function requireSuperAdmin()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $requesterId = (int)($data['requester_id'] ?? $this->request->getGet('requester_id') ?? 0);
        
        if (!$requesterId) {
            return null;
        }

        $userModel = new UserModel();
        $requester = $userModel->find($requesterId);

        if (!$requester || $requester->role !== 'super_admin') {
            return null;
        }

        return $requester;
    }

    public function index()
    {
        if (!$this->requireSuperAdmin()) {
            return $this->respond(['success' => false, 'message' => 'Super admin access required'], 403);
        }

        $userModel = new UserModel();
        $users = $userModel->select('user_id, username, email, full_name, role, is_active, created_at')
                          ->orderBy('created_at', 'DESC')
                          ->findAll();

        return $this->respond(['success' => true, 'data' => $users]);
    }

    public function assignAdmin()
    {
        if (!$this->requireSuperAdmin()) {
            return $this->respond(['success' => false, 'message' => 'Super admin access required'], 403);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $targetUserId = (int)($data['user_id'] ?? 0);
        $newRole = $data['role'] ?? 'user';

        if (!in_array($newRole, ['user', 'admin'])) {
            return $this->respond(['success' => false, 'message' => 'Invalid role. Only user or admin allowed.'], 400);
        }

        $userModel = new UserModel();
        $target = $userModel->find($targetUserId);

        if (!$target) {
            return $this->respond(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($target->role === 'super_admin') {
            return $this->respond(['success' => false, 'message' => 'Cannot modify super admin'], 400);
        }

        $userModel->update($targetUserId, ['role' => $newRole]);

        return $this->respond([
            'success' => true,
            'message' => "User role updated to {$newRole}"
        ]);
    }

    public function createUser()
    {
        if (!$this->requireSuperAdmin()) {
            return $this->respond(['success' => false, 'message' => 'Super admin access required'], 403);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|min_length[3]',
            'role' => 'required|in_list[user,admin]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['success' => false, 'errors' => $this->validator->getErrors()], 400);
        }

        $userModel = new UserModel();
        $id = $userModel->insert([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'],
            'role' => $data['role'],
            'is_active' => 1
        ]);

        return $this->respond(['success' => true, 'message' => 'User created', 'user_id' => $id], 201);
    }
}