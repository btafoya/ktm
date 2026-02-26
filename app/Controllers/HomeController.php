<?php

namespace App\Controllers;

use App\Models\BoardModel;

class HomeController extends BaseController
{
    protected $boardModel;

    public function __construct()
    {
        $this->boardModel = new BoardModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('auth/login');
        }

        $boards = $this->boardModel->getForUser($userId);
        $defaultBoard = $this->boardModel->getDefaultBoard($userId);

        if ($defaultBoard) {
            return redirect()->to("boards/{$defaultBoard['id']}");
        }

        return redirect()->to('boards');
    }
}