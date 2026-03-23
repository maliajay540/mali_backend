<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MessageModel;
use App\Models\InterestModel;
use App\Libraries\UserContext;

class Chat extends ResourceController
{
    protected $modelName = 'App\Models\MessageModel';
    protected $format = 'json';

    public function send()
    {
        $senderId = UserContext::getUserId();
        $receiverId = $this->request->getVar('receiver_id');
        $message = $this->request->getVar('message');

        if (!$receiverId || !$message) {
            return $this->fail('Receiver ID and message required');
        }

        // Check if sender is premium
        if (!UserContext::isPremium()) {
            return $this->failForbidden('Upgrade to premium to chat');
        }

        // Check interest status
        $interestModel = new InterestModel();
        $interest = $interestModel->where('status', 'accepted')
                                  ->groupStart()
                                      ->where('sender_id', $senderId)->where('receiver_id', $receiverId)
                                      ->orGroupStart()
                                          ->where('sender_id', $receiverId)->where('receiver_id', $senderId)
                                      ->groupEnd()
                                  ->groupEnd()
                                  ->first();

        if (!$interest) {
            return $this->failForbidden('Interest not accepted or does not exist');
        }

        $model = new MessageModel();
        $data = [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => htmlspecialchars($message) // XSS protection
        ];

        if ($model->insert($data)) {
            return $this->respondCreated(['status' => true, 'message' => 'Message sent']);
        } else {
            return $this->failValidationErrors($model->errors());
        }
    }

    public function messages()
    {
        $userId = UserContext::getUserId();
        $partnerId = $this->request->getVar('partner_id');

        // Allow user1/user2 params as per requirement, but validate
        $user1 = $this->request->getVar('user1');
        $user2 = $this->request->getVar('user2');

        if ($user1 && $user2) {
            if ($user1 != $userId && $user2 != $userId) {
                return $this->failForbidden('Access denied');
            }
            $partnerId = ($user1 == $userId) ? $user2 : $user1;
        }

        if (!$partnerId) {
            return $this->fail('Partner ID required');
        }

        $model = new MessageModel();
        $messages = $model->groupStart()
                                ->where('sender_id', $userId)->where('receiver_id', $partnerId)
                          ->groupEnd()
                          ->orGroupStart()
                                ->where('sender_id', $partnerId)->where('receiver_id', $userId)
                          ->groupEnd()
                          ->orderBy('created_at', 'ASC')
                          ->findAll();

        return $this->respond(['status' => true, 'data' => $messages]);
    }
}
