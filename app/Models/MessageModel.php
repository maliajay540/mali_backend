<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sender_id', 'receiver_id', 'message'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'sender_id'   => 'required|integer',
        'receiver_id' => 'required|integer',
        'message'     => 'required'
    ];
}
