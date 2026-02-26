<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GoogleTokenModel;
use App\Models\GoogleCalendarModel;

class SettingsController extends BaseController
{
    protected $userModel;
    protected $googleTokenModel;
    protected $googleCalendarModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->googleTokenModel = new GoogleTokenModel();
        $this->googleCalendarModel = new GoogleCalendarModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $googleToken = $this->googleTokenModel->getForUser($userId);
        $calendars = $this->googleCalendarModel->getForUser($userId);

        return view('settings/index', [
            'user' => $user,
            'googleToken' => $googleToken,
            'calendars' => $calendars,
        ]);
    }

    public function updateProfile()
    {
        $rules = [
            'full_name' => 'permit_empty|string|max_length[255]',
            'email' => 'permit_empty|valid_email|is_unique[users.email,id,{id}]',
            'timezone' => 'permit_empty|string|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $updateData = ['updated_at' => date('Y-m-d H:i:s')];

        $fullName = $this->request->getPost('full_name');
        if ($fullName !== null) {
            $updateData['full_name'] = $fullName;
            session()->set('full_name', $fullName);
        }

        $email = $this->request->getPost('email');
        if ($email !== null && $email !== '') {
            $updateData['email'] = $email;
            session()->set('email', $email);
        }

        $timezone = $this->request->getPost('timezone');
        if ($timezone !== null && $timezone !== '') {
            $updateData['timezone'] = $timezone;
        }

        $this->userModel->update($userId, $updateData);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $this->userModel->update($userId, [
            'password_hash' => $this->request->getPost('new_password'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}