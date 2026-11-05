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
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]|alpha_numeric',
            'first_name' => 'required|max_length[500]|alpha_space',
            'middle_name' => 'permit_empty|max_length[500]|alpha_space',
            'last_name' => 'required|max_length[500]|alpha_space',
            'extension_name' => 'permit_empty|max_length[50]|alpha_space',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'confirm_password' => 'required|matches[password]',
            'atbi_affiliation' => 'required|alpha_space',
            'phone_number' => 'required|numeric|min_length[10]|max_length[15]'
        ];

        $messages = [
            'username' => ['is_unique' => 'This username is already taken.', 'alpha_numeric' => 'Username can only contain letters and numbers.'],
            'first_name' => ['alpha_space' => 'First name must contain letters only.'],
            'last_name' => ['alpha_space' => 'Last name must contain letters only.'],
            'email' => ['valid_email' => 'Please enter a valid email address.', 'is_unique' => 'This email is already registered.'],
            'password' => ['regex_match' => 'Password must contain at least 1 uppercase, 1 lowercase, and 1 number.'],
            'confirm_password' => ['matches' => 'Passwords do not match.'],
            'atbi_affiliation' => ['alpha_space' => 'ATBI affiliation must contain letters only.'],
            'phone_number' => ['numeric' => 'Phone number must contain numbers only.']
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->respond(['success' => false, 'errors' => $this->validator->getErrors()], 400);
        }

        $fullName = trim($data['first_name'] . ' ' . $data['middle_name'] . ' ' . $data['last_name'] . ' ' . $data['extension_name']);

        $userModel = new UserModel();
        $userId = $userModel->insert([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $fullName,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'extension_name' => $data['extension_name'] ?? null,
            'atbi_affiliation' => $data['atbi_affiliation'],
            'phone_number' => $data['phone_number'],
            'role' => 'user',
            'is_active' => 1,
            'status' => 'pending'
        ]);

        return $this->respond(['success' => true, 'message' => 'Registration submitted. Please wait for admin approval.', 'user_id' => $userId], 201);
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
        $status = is_array($user) ? $user['status'] : $user->status;

        if (!password_verify($data['password'], $password)) {
            return $this->respond(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        if (!$isActive) {
            return $this->respond(['success' => false, 'message' => 'Account deactivated'], 403);
        }

        if ($status === 'pending') {
            return $this->respond(['success' => false, 'message' => 'Your account is pending admin approval.'], 403);
        }

        if ($status === 'rejected') {
            return $this->respond(['success' => false, 'message' => 'Your registration was rejected.'], 403);
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