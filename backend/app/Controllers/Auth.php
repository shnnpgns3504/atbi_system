<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function register()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['success' => false, 'errors' => $this->validator->getErrors()], 400);
        }

        $userModel = new UserModel();
        $userId = $userModel->insert([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'],
            'role' => 'user',
            'is_active' => 1
        ]);

        return $this->respond(['success' => true, 'message' => 'Account created', 'user_id' => $userId], 201);
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $userModel = new UserModel();
        $user = $userModel->where('username', $data['username'])->first();

        if (!$user) {
            return $this->respond(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $password = is_array($user) ? $user['password'] : $user->password;
        $isActive = is_array($user) ? $user['is_active'] : $user->is_active;

        if (!password_verify($data['password'], $password)) {
            return $this->respond(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        if (!$isActive) {
            return $this->respond(['success' => false, 'message' => 'Account deactivated'], 403);
        }

        $token = bin2hex(random_bytes(32));

        return $this->respond([
            'success' => true,
            'token' => $token,
            'user' => [
                'user_id' => is_array($user) ? $user['user_id'] : $user->user_id,
                'username' => is_array($user) ? $user['username'] : $user->username,
                'email' => is_array($user) ? $user['email'] : $user->email,
                'full_name' => is_array($user) ? $user['full_name'] : $user->full_name,
                'role' => is_array($user) ? $user['role'] : $user->role,
            ]
        ]);
    }
}