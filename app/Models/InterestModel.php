<?php

namespace App\Models;

use CodeIgniter\Model;

class InterestModel extends Model
{
    protected $table = 'interests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sender_id', 'receiver_id', 'status'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'sender_id'   => 'required|integer',
        'receiver_id' => 'required|integer',
        'status'      => 'in_list[pending,accepted,rejected]'
    ];
}
