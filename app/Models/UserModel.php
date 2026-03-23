<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'phone', 'name', 'password', 'profile_complete',
        'is_premium', 'premium_expiry'
    ];
    protected $useTimestamps = true;

    protected $validationRules = [
        'phone' => 'required|min_length[10]|is_unique[users.phone,id,{id}]',
        'name'  => 'required|min_length[3]',
    ];
}
