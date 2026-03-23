<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table = 'profiles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'name', 'age', 'city', 'profession', 'education',
        'bio', 'image', 'height', 'weight', 'religion', 'gotra',
        'mother_tongue', 'marital_status', 'family_type', 'father_name',
        'father_profession', 'mother_name', 'mother_profession',
        'siblings', 'income', 'hobbies', 'phone', 'whatsapp'
    ];
    protected $useTimestamps = true;

    protected $validationRules = [
        'user_id' => 'required|integer',
        'name'    => 'required|min_length[3]',
        'age'     => 'required|integer|greater_than_equal_to[18]',
        'city'    => 'required',
        'profession' => 'required'
    ];
}
