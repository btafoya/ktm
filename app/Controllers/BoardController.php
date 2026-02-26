<?php

namespace App\Controllers;

use App\Models\BoardModel;
use App\Models\ColumnModel;

class BoardController extends BaseController
{
    protected $boardModel;
    protected $columnModel;

    public function __construct()
    {
        $this->boardModel = new BoardModel();
        $this->columnModel = new ColumnModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $boards = $this->boardModel->getForUser($userId);

        return view('boards/index', ['boards' => $boards]);
    }

    public function show($id)
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] != $userId) {
            return redirect()->to('boards')->with('error', 'Board not found.');
        }

        $board = $this->boardModel->getWithColumns($id);

        $cardModel = new \App\Models\CardModel();
        foreach ($board['columns'] as &$column) {
            $column['cards'] = $cardModel->getForColumn($column['id']);
        }

        return view('boards/show', ['board' => $board]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'description' => 'permit_empty|max_length[500]',
                'background_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $userId = session()->get('user_id');

            $boardId = $this->boardModel->insert([
                'user_id' => $userId,
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_public' => false,
                'is_default' => false,
                'background_color' => $this->request->getPost('background_color') ?: '#212529',
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

                return redirect()->to("boards/{$boardId}");
            }

            return redirect()->back()->with('error', 'Failed to create board.');
        }

        return view('boards/create');
    }

    public function edit($id)
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] != $userId) {
            return redirect()->to('boards')->with('error', 'Board not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'description' => 'permit_empty|max_length[500]',
                'background_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $updated = $this->boardModel->update($id, [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'background_color' => $this->request->getPost('background_color') ?: '#212529',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($updated) {
                return redirect()->to("boards/{$id}");
            }

            return redirect()->back()->with('error', 'Failed to update board.');
        }

        return view('boards/edit', ['board' => $board]);
    }

    public function delete($id)
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Board not found.']);
        }

        $deleted = $this->boardModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Board deleted successfully.' : 'Failed to delete board.',
        ]);
    }

    public function setDefault($id)
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Board not found.']);
        }

        $this->boardModel->where('user_id', $userId)->update(null, ['is_default' => false]);
        $updated = $this->boardModel->update($id, ['is_default' => true, 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Default board updated.' : 'Failed to update default board.',
        ]);
    }

    public function reorderColumns($id)
    {
        $userId = session()->get('user_id');
        $board = $this->boardModel->find($id);

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Board not found.']);
        }

        $columnIds = $this->request->getJSON(true)['column_ids'] ?? [];

        $success = $this->columnModel->reorder($id, $columnIds);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Columns reordered.' : 'Failed to reorder columns.',
        ]);
    }
}