<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
    }

    /**
     * Show login form
     */
    public function login(): string
    {
        return view('auth/login', [
            'title' => 'Login - Kanban Task Manager',
        ]);
    }

    /**
     * Attempt to login
     */
    public function attemptLogin(): ResponseInterface
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        if (!$this->userModel->verifyPassword($email, $password)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password');
        }

        $user = $this->userModel->findByEmail($email);

        // Set session
        session()->set([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'display_name' => $user['display_name'] ?? explode('@', $user['email'])[0],
        ]);

        return redirect()->to('/')->with('success', 'Welcome back!');
    }

    /**
     * Show registration form
     */
    public function register(): string
    {
        return view('auth/register', [
            'title' => 'Register - Kanban Task Manager',
        ]);
    }

    /**
     * Attempt to register
     */
    public function attemptRegister(): ResponseInterface
    {
        $rules = [
            'email'                 => 'required|valid_email|is_unique[users.email]',
            'password'              => 'required|min_length[8]',
            'password_confirm'      => 'required|matches[password]',
            'display_name'          => 'permit_empty|string|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userId = $this->userModel->insert([
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'display_name' => $this->request->getPost('display_name') ?: null,
        ]);

        if (!$userId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create account');
        }

        // Create default board for new user
        $this->createDefaultBoard($userId);

        // Auto-login
        session()->set([
            'user_id' => $userId,
            'email' => $this->request->getPost('email'),
            'display_name' => $this->request->getPost('display_name') ?: explode('@', $this->request->getPost('email'))[0],
        ]);

        return redirect()->to('/')->with('success', 'Account created! Welcome to Kanban Task Manager');
    }

    /**
     * Logout
     */
    public function logout(): ResponseInterface
    {
        session()->destroy();

        return redirect()->to('auth/login')->with('success', 'You have been logged out');
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword(): string
    {
        return view('auth/forgot_password', [
            'title' => 'Forgot Password - Kanban Task Manager',
        ]);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(): ResponseInterface
    {
        $email = $this->request->getPost('email');

        if (!$this->validate(['email' => 'required|valid_email'])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->findByEmail($email);

        // Always show success message to prevent email enumeration
        // Only send email if user exists
        if ($user) {
            $this->sendPasswordResetEmail($user);
        }

        return redirect()->back()
            ->with('success', 'If an account with that email exists, you will receive a password reset link');
    }

    /**
     * Show reset password form
     */
    public function resetPassword(string $token): string|ResponseInterface
    {
        // Validate token
        $db = db_connect();
        $reset = $db->table('password_resets')
            ->where('token', $token)
            ->where('created_at >', date('Y-m-d H:i:s', time() - 3600)) // 1 hour expiry
            ->first();

        if (!$reset) {
            return redirect()->to('auth/forgot-password')
                ->with('error', 'Invalid or expired reset link');
        }

        return view('auth/reset_password', [
            'title' => 'Reset Password - Kanban Task Manager',
            'token' => $token,
        ]);
    }

    /**
     * Submit new password
     */
    public function resetPasswordSubmit(): ResponseInterface
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $rules = [
            'token'                 => 'required',
            'password'              => 'required|min_length[8]',
            'password_confirm'      => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $reset = $db->table('password_resets')
            ->where('token', $token)
            ->where('created_at >', date('Y-m-d H:i:s', time() - 3600))
            ->first();

        if (!$reset) {
            return redirect()->to('auth/forgot-password')
                ->with('error', 'Invalid or expired reset link');
        }

        // Update password
        $this->userModel->update($reset['email'], ['password' => $password]); // Will be hashed by model

        // Delete reset token
        $db->table('password_resets')->where('token', $token)->delete();

        return redirect()->to('auth/login')
            ->with('success', 'Password has been reset. Please log in with your new password');
    }

    /**
     * Create default board for new user
     */
    private function createDefaultBoard(int $userId): void
    {
        $db = db_connect();

        // Create board
        $boardId = $db->table('boards')->insertGetId([
            'user_id' => $userId,
            'name' => 'My First Board',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create default columns
        $columns = [
            ['name' => 'To Do', 'position' => 0],
            ['name' => 'In Progress', 'position' => 1],
            ['name' => 'Done', 'position' => 2],
        ];

        foreach ($columns as $column) {
            $db->table('columns')->insert([
                'board_id' => $boardId,
                'name' => $column['name'],
                'position' => $column['position'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(array $user): void
    {
        $token = bin2hex(random_bytes(32));
        $db = db_connect();

        // Delete existing tokens for this email
        $db->table('password_resets')->where('email', $user['email'])->delete();

        // Store new token
        $db->table('password_resets')->insert([
            'email' => $user['email'],
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Send email (using built-in CI4 email library)
        $email = \Config\Services::email();
        $resetLink = site_url("auth/reset-password/{$token}");

        $email->setTo($user['email']);
        $email->setSubject('Password Reset - Kanban Task Manager');
        $email->setMessage(view('emails/password_reset', [
            'user' => $user,
            'resetLink' => $resetLink,
        ]));

        $email->send();
    }
}