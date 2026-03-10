<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{

    private $secret = "SUPER_SECRET_KEY_123";

    public function before(RequestInterface $request, $arguments = null)
    {

        $header = $request->getHeaderLine("Authorization");

        if(!$header){
            return service('response')->setJSON([
                "status" => false,
                "message" => "Token missing"
            ]);
        }

        $token = str_replace("Bearer ", "", $header);

        try{

            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));

        }catch(\Exception $e){

            return service('response')->setJSON([
                "status" => false,
                "message" => "Invalid token"
            ]);

        }

    }

}