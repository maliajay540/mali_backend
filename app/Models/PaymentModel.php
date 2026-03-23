<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'plan', 'amount', 'transaction_id', 'status'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'user_id' => 'required|integer',
        'amount'  => 'required|decimal',
        'status'  => 'required'
    ];
}
