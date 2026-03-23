<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProfileModel;
use App\Libraries\UserContext;

class Profiles extends ResourceController
{
    protected $modelName = 'App\Models\ProfileModel';
    protected $format = 'json';

    public function index()
    {
        $city = $this->request->getVar('city');
        $religion = $this->request->getVar('religion');
        $age = $this->request->getVar('age');

        $model = new ProfileModel();

        if ($city) $model->where('city', $city);
        if ($religion) $model->where('religion', $religion);
        if ($age) $model->where('age', $age);

        $profiles = $model->findAll();

        // Hide contact info if viewer is not premium
        $isPremium = UserContext::isPremium();

        if (!$isPremium) {
            foreach ($profiles as &$p) {
                unset($p['phone']);
                unset($p['whatsapp']);
            }
        }

        return $this->respond(['status' => true, 'data' => $profiles]);
    }

    public function show($id = null)
    {
        $model = new ProfileModel();
        $profile = $model->find($id);

        if (!$profile) return $this->failNotFound('Profile not found');

        $isPremium = UserContext::isPremium();

        if (!$isPremium) {
            unset($profile['phone']);
            unset($profile['whatsapp']);
        }

        return $this->respond(['status' => true, 'data' => $profile]);
    }

    public function create()
    {
        $model = new ProfileModel();
        $data = $this->request->getJSON(true); 
        $data['user_id'] = UserContext::getUserId();

        if ($model->insert($data)) {
            return $this->respondCreated(['status' => true, 'message' => 'Profile created', 'id' => $model->getInsertID()]);
        } else {
            return $this->failValidationErrors($model->errors());
        }
    }

    public function update($id = null)
    {
        $model = new ProfileModel();
        $data = $this->request->getJSON(true);

        $profile = $model->find($id);
        if (!$profile) return $this->failNotFound('Profile not found');

        if ($profile['user_id'] != UserContext::getUserId()) {
            return $this->failForbidden('You can only update your own profile');
        }

        if ($model->update($id, $data)) {
            return $this->respond(['status' => true, 'message' => 'Profile updated']);
        } else {
            return $this->failValidationErrors($model->errors());
        }
    }

    public function delete($id = null)
    {
        $model = new ProfileModel();
        $profile = $model->find($id);

        if (!$profile) return $this->failNotFound('Profile not found');

        if ($profile['user_id'] != UserContext::getUserId()) {
            return $this->failForbidden('You can only delete your own profile');
        }

        if ($model->delete($id)) {
            return $this->respondDeleted(['status' => true, 'message' => 'Profile deleted']);
        } else {
            return $this->failServerError('Could not delete profile');
        }
    }
}
