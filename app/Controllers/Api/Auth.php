<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class Auth extends ResourceController
{

   private $secret = "a8d9f7e4b2c1a9d8e7f6c5b4a3d2e1f0a9b8c7d6e5f4";
   public function login()
{
    try {

        $data = $this->request->getJSON(true);
        $phone = $data['phone'] ?? null;

        if (!$phone) {
            return $this->respond([
                "status" => false,
                "message" => "Phone required"
            ], 400);
        }

        $db = \Config\Database::connect();

        $user = $db->table('users')
            ->where('phone', $phone)
            ->get()
            ->getRow();

        if (!$user) {

            $db->table('users')->insert([
                "phone" => $phone
            ]);

            $userId = $db->insertID();

        } else {

            $userId = $user->id;

        }

        return $this->respond([
            "status" => true,
            "user_id" => $userId
        ]);

    } catch (\Throwable $e) {

        return $this->respond([
            "status" => false,
            "error" => $e->getMessage()
        ], 500);

    }
}

}