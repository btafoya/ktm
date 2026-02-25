<?php

namespace App\Controllers;

use App\Models\BoardModel;
use App\Models\ColumnModel;
use App\Models\CardModel;
use CodeIgniter\HTTP\ResponseInterface;

class BoardController extends BaseController
{
    protected BoardModel $boardModel;
    protected ColumnModel $columnModel;
    protected CardModel $cardModel;

    public function __construct()
    {
        $this->boardModel = model(BoardModel::class);
        $this->columnModel = model(ColumnModel::class);
        $this->cardModel = model(CardModel::class);
    }

    /**
     * Dashboard - list boards
     */
    public function index(): string
    {
        $userId = session()->get('user_id');

        $boards = $this->boardModel->getForUser($userId);

        return view('boards/index', [
            'title' => 'My Boards - Kanban Task Manager',
            'boards' => $boards,
            'displayName' => session()->get('display_name'),
        ]);
    }

    /**
     * Show create board form
     */
    public function create(): string
    {
        return view('boards/create', [
            'title' => 'Create Board - Kanban Task Manager',
        ]);
    }

    /**
     * Store new board
     */
    public function store(): ResponseInterface
    {
        $userId = session()->get('user_id');

        if (!$this->validate(['name' => 'required|string|max_length[255]'])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $boardId = $this->boardModel->insert([
            'user_id' => $userId,
            'name' => $this->request->getPost('name'),
        ]);

        if (!$boardId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create board');
        }

        // Create default columns
        $this->createDefaultColumns($boardId);

        return redirect()->to('boards/' . $boardId)->with('success', 'Board created!');
    }

    /**
     * Show board with kanban view
     */
    public function show(int $id): string|ResponseInterface
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] !== $userId) {
            return redirect()->to('/')->with('error', 'Board not found');
        }

        $columns = $this->columnModel->getForBoard($id);

        // Load cards for each column
        foreach ($columns as &$column) {
            $column['cards'] = $this->cardModel->getForColumn($column['id']);
        }

        // Get all user's tags
        $tags = model('App\Models\TagModel')->getAll();

        return view('boards/show', [
            'title' => $board['name'] . ' - Kanban Task Manager',
            'board' => $board,
            'columns' => $columns,
            'tags' => $tags,
        ]);
    }

    /**
     * Show edit board form
     */
    public function edit(int $id): string|ResponseInterface
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] !== $userId) {
            return redirect()->to('/')->with('error', 'Board not found');
        }

        return view('boards/edit', [
            'title' => 'Edit Board - Kanban Task Manager',
            'board' => $board,
        ]);
    }

    /**
     * Update board
     */
    public function update(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] !== $userId) {
            return redirect()->to('/')->with('error', 'Board not found');
        }

        if (!$this->validate(['name' => 'required|string|max_length[255]'])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        if (!$this->boardModel->update($id, [
            'name' => $this->request->getPost('name'),
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update board');
        }

        return redirect()->to('boards/' . $id)->with('success', 'Board updated!');
    }

    /**
     * Archive board
     */
    public function archive(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Board not found'], 404);
        }

        $this->boardModel->archive($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Board archived']);
    }

    /**
     * Delete board
     */
    public function delete(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] !== $userId) {
            return redirect()->to('/')->with('error', 'Board not found');
        }

        if (!$this->boardModel->delete($id)) {
            return redirect()->back()->with('error', 'Failed to delete board');
        }

        return redirect()->to('/')->with('success', 'Board deleted');
    }

    /**
     * Create default columns for new board
     */
    private function createDefaultColumns(int $boardId): void
    {
        $defaultColumns = [
            'To Do',
            'In Progress',
            'Done',
        ];

        foreach ($defaultColumns as $index => $name) {
            $this->columnModel->insert([
                'board_id' => $boardId,
                'name' => $name,
                'position' => $index,
            ]);
        }
    }
}