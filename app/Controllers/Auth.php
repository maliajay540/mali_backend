<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Config\Services;

class Auth extends ResourceController
{
    public function login()
    {
        // Rate limiting: 5 requests per minute
        $throttler = Services::throttler();
        $ip = $this->request->getIPAddress();
        if ($throttler->check($ip, 5, 60) === false) {
            return $this->respond(['status' => false, 'message' => 'Too many requests. Please wait.'], 429);
        }

        // Require password
        $rules = [
            'phone' => 'required|min_length[10]|max_length[15]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => false,
                'message' => $this->validator->getErrors()
            ], 400);
        }

        $phone = $this->request->getVar('phone');
        $password = $this->request->getVar('password');

        $userModel = new UserModel();
        $user = $userModel->where('phone', $phone)->first();

        if (!$user) {
            // Auto register
            $data = [
                'phone' => $phone,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'name' => 'User ' . substr($phone, -4),
            ];
            $userModel->save($data);
            $user = $userModel->where('phone', $phone)->first();
        } else {
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return $this->respond(['status' => false, 'message' => 'Invalid credentials'], 401);
            }
        }

        // Generate JWT
        $key = getenv('JWT_SECRET');
        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'exp' => time() + (30 * 24 * 60 * 60), // 30 days
            'uid' => $user['id'],
            'phone' => $user['phone'],
            'is_premium' => $user['is_premium']
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => [
                'id' => $user['id'],
                'phone' => $user['phone'],
                'name' => $user['name'],
                'is_premium' => (bool)$user['is_premium']
            ]
        ]);
    }
}
