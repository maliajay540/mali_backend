<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InterestModel;
use CodeIgniter\I18n\Time;
use App\Libraries\UserContext;

class Interests extends ResourceController
{
    protected $modelName = 'App\Models\InterestModel';
    protected $format = 'json';

    public function send()
    {
        $senderId = UserContext::getUserId();
        $receiverId = $this->request->getVar('receiver_id');

        if (!$receiverId) {
            return $this->fail('Receiver ID required');
        }

        if ($senderId == $receiverId) {
            return $this->fail('Cannot send interest to self');
        }

        $model = new InterestModel();

        // Check duplicate
        $existing = $model->where('sender_id', $senderId)
                          ->where('receiver_id', $receiverId)
                          ->first();
        if ($existing) {
            return $this->fail('Interest already sent');
        }

        // Check limit for free users
        if (!UserContext::isPremium()) {
            $today = Time::now()->toDateString();
            $count = $model->where('sender_id', $senderId)
                           ->like('created_at', $today)
                           ->countAllResults();
            if ($count >= 5) {
                return $this->fail('Daily limit reached for free users');
            }
        }

        $data = [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending'
        ];

        if ($model->insert($data)) {
            return $this->respondCreated(['status' => true, 'message' => 'Interest sent']);
        } else {
            return $this->failValidationErrors($model->errors());
        }
    }

    public function respond()
    {
        $id = $this->request->getVar('interest_id');
        $status = $this->request->getVar('status'); // accepted, rejected

        if (!in_array($status, ['accepted', 'rejected'])) {
            return $this->fail('Invalid status');
        }

        $model = new InterestModel();
        $interest = $model->find($id);

        if (!$interest) {
            return $this->failNotFound('Interest not found');
        }

        // Only receiver can respond
        if ($interest['receiver_id'] != UserContext::getUserId()) {
            return $this->failForbidden('You are not authorized to respond to this interest');
        }

        $model->update($id, ['status' => $status]);

        return $this->respond(['status' => true, 'message' => 'Interest ' . $status]);
    }
}
