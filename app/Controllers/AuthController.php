<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BoardModel;
use App\Models\ColumnModel;
use App\Models\PasswordResetModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $boardModel;
    protected $columnModel;
    protected $passwordResetModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->boardModel = new BoardModel();
        $this->columnModel = new ColumnModel();
        $this->passwordResetModel = new PasswordResetModel();
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $user = $this->userModel->findByEmail($email);

            if ($user && $this->userModel->verifyPassword($password, $user['password_hash'])) {
                if (!$user['is_active']) {
                    return redirect()->back()->with('error', 'Your account is inactive. Please contact support.');
                }

                session()->set([
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'logged_in' => true,
                ]);

                $defaultBoard = $this->boardModel->getDefaultBoard($user['id']);
                if ($defaultBoard) {
                    return redirect()->to("boards/{$defaultBoard['id']}");
                }

                return redirect()->to('boards');
            }

            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
                'full_name' => 'permit_empty|string|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $userData = [
                'email' => $this->request->getPost('email'),
                'password_hash' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'timezone' => 'UTC',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $userId = $this->userModel->insert($userData);

            if ($userId) {
                $boardId = $this->boardModel->insert([
                    'user_id' => $userId,
                    'name' => 'My Board',
                    'description' => 'My first kanban board',
                    'is_public' => false,
                    'is_default' => true,
                    'column_order' => json_encode([]),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                if ($boardId) {
                    $defaultColumns = [
                        ['Backlog', '#6c757d', 0],
                        ['To Do', '#0d6efd', 1],
                        ['In Progress', '#ffc107', 2],
                        ['Done', '#198754', 3],
                    ];

                    foreach ($defaultColumns as $col) {
                        $this->columnModel->insert([
                            'board_id' => $boardId,
                            'name' => $col[0],
                            'color' => $col[1],
                            'position' => $col[2],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                session()->set([
                    'user_id' => $userId,
                    'email' => $userData['email'],
                    'full_name' => $userData['full_name'],
                    'logged_in' => true,
                ]);

                return redirect()->to("boards/{$boardId}");
            }

            return redirect()->back()->with('error', 'Failed to create account. Please try again.');
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login')->with('success', 'You have been logged out.');
    }

    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');

            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                return redirect()->back()->with('success', 'If an account exists with that email, password reset instructions have been sent.');
            }

            $token = $this->passwordResetModel->createToken($email);

            $resetUrl = base_url("auth/reset-password?token={$token}");

            $emailMessage = view('emails/password_reset', ['resetUrl' => $resetUrl, 'full_name' => $user['full_name'] ?? 'User']);

            $emailService = \Config\Services::email();
            $emailService->setTo($email);
            $emailService->setSubject('Password Reset Request');
            $emailService->setMessage($emailMessage);

            if ($emailService->send()) {
                return redirect()->back()->with('success', 'Password reset instructions have been sent to your email.');
            }

            return redirect()->back()->with('error', 'Failed to send password reset email. Please try again.');
        }

        return view('auth/forgot_password');
    }

    public function resetPassword()
    {
        $token = $this->request->getGet('token');

        if (!$token || $this->passwordResetModel->isExpired($token)) {
            return redirect()->to('auth/forgot-password')->with('error', 'Invalid or expired reset token.');
        }

        $reset = $this->passwordResetModel->findByToken($token);
        if (!$reset) {
            return redirect()->to('auth/forgot-password')->with('error', 'Invalid reset token.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'password' => 'required|min_length[8]',
                'password_confirm' => 'required|matches[password]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->with('errors', $this->validator->getErrors());
            }

            $user = $this->userModel->findByEmail($reset['email']);
            if ($user) {
                $this->userModel->update($user['id'], [
                    'password_hash' => $this->request->getPost('password'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->passwordResetModel->deleteByEmail($reset['email']);

            return redirect()->to('auth/login')->with('success', 'Your password has been reset. Please log in.');
        }

        return view('auth/reset_password', ['token' => $token]);
    }
}