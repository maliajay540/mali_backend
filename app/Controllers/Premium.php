<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PaymentModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class Premium extends ResourceController
{
    protected $modelName = 'App\Models\PaymentModel';
    protected $format = 'json';

    public function subscribe()
    {
        $plan = $this->request->getVar('plan');
        $amount = $this->request->getVar('amount');
        $transactionId = $this->request->getVar('transaction_id');

        if (!$plan || !$amount || !$transactionId) {
            return $this->fail('Plan, amount, and transaction ID required');
        }

        $userId = $this->request->user->uid;

        // Mock payment verification
        $data = [
            'user_id' => $userId,
            'plan' => $plan,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'status' => 'success'
        ];

        $paymentModel = new PaymentModel();
        if ($paymentModel->insert($data)) {
            // Upgrade user
            $userModel = new UserModel();
            $expiry = Time::now()->addMonths(1)->toDateString();
            if ($plan == 'yearly') {
                $expiry = Time::now()->addYears(1)->toDateString();
            } else if ($plan == 'monthly') {
                $expiry = Time::now()->addMonths(1)->toDateString();
            } else {
                 $expiry = Time::now()->addMonths(1)->toDateString(); // Default
            }

            $userModel->update($userId, [
                'is_premium' => 1,
                'premium_expiry' => $expiry
            ]);

            return $this->respondCreated(['status' => true, 'message' => 'Subscription successful', 'expiry' => $expiry]);
        } else {
            return $this->failValidationErrors($paymentModel->errors());
        }
    }
}
