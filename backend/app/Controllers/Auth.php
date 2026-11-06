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

        // 1. Check if currently locked out
        if ($user->login_lockout_until && strtotime($user->login_lockout_until) > time()) {
            $secondsLeft = strtotime($user->login_lockout_until) - time();
            return $this->respond(['success' => false, 'message' => "Too many failed attempts. Try again in {$secondsLeft} seconds."], 429);
        }

        // 2. Verify the password
        if (!password_verify($data['password'], $user->password)) {
            $attempts = $user->login_attempts + 1;
            
            if ($attempts >= 4) {
                // Lock out for 30 seconds
                $lockout = date('Y-m-d H:i:s', strtotime('+30 seconds'));
                $userModel->update($user->user_id, ['login_attempts' => 0, 'login_lockout_until' => $lockout]);
                return $this->respond(['success' => false, 'message' => 'Too many failed attempts. You are locked out for 30 seconds.'], 429);
            } else {
                $userModel->update($user->user_id, ['login_attempts' => $attempts]);
                $remaining = 4 - $attempts;
                return $this->respond(['success' => false, 'message' => "Invalid credentials. {$remaining} attempts remaining."], 401);
            }
        }

        // 3. Success - Clear attempts and proceed with status checks
        $userModel->update($user->user_id, ['login_attempts' => 0, 'login_lockout_until' => null]);

        if (!$user->is_active) {
            return $this->respond(['success' => false, 'message' => 'Account deactivated'], 403);
        }

        if ($user->status === 'pending') {
            return $this->respond(['success' => false, 'message' => 'Your account is pending admin approval.'], 403);
        }

        if ($user->status === 'rejected') {
            return $this->respond(['success' => false, 'message' => 'Your registration was rejected.'], 403);
        }

        $token = bin2hex(random_bytes(32));

        return $this->respond([
            'success' => true,
            'token' => $token,
            'user' => [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'role' => $user->role,
                'atbi_affiliation' => $user->atbi_affiliation
            ]
        ]);
    }
}