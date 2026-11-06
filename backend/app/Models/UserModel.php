<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
        'username', 'email', 'password', 'full_name', 
        'first_name', 'middle_name', 'last_name', 'extension_name',
        'atbi_affiliation', 'phone_number', 'role', 
        'is_active', 'status', 'reset_otp', 'reset_otp_expires', 'otp_attempts', 
        'otp_lockout_until', 'login_attempts', 'login_lockout_until'
    ];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    // ONLY hash on insert. Do NOT hash on update.
    protected $beforeInsert = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}