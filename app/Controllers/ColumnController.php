<?php

namespace App\Controllers;

use App\Models\ColumnModel;
use App\Models\BoardModel;

class ColumnController extends BaseController
{
    protected $columnModel;
    protected $boardModel;

    public function __construct()
    {
        $this->columnModel = new ColumnModel();
        $this->boardModel = new BoardModel();
    }

    public function create()
    {
        $rules = [
            'board_id' => 'required|numeric',
            'name' => 'required|min_length[1]|max_length[100]',
            'color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $userId = session()->get('user_id');
        $boardId = $this->request->getPost('board_id');
        $board = $this->boardModel->find($boardId);

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Board not found.']);
        }

        $maxPosition = $this->columnModel->where('board_id', $boardId)->selectMax('position')->first()['position'] ?? -1;

        $columnId = $this->columnModel->insert([
            'board_id' => $boardId,
            'name' => $this->request->getPost('name'),
            'color' => $this->request->getPost('color') ?: '#0d6efd',
            'position' => $maxPosition + 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($columnId) {
            $column = $this->columnModel->find($columnId);
            $column['cards'] = [];
            return $this->response->setJSON(['success' => true, 'column' => $column]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create column.']);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[1]|max_length[100]',
            'color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $column = $this->columnModel->find($id);
        if (!$column) {
            return $this->response->setJSON(['success' => false, 'message' => 'Column not found.']);
        }

        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $updated = $this->columnModel->update($id, [
            'name' => $this->request->getPost('name'),
            'color' => $this->request->getPost('color') ?: '#0d6efd',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Column updated.' : 'Failed to update column.',
        ]);
    }

    public function delete($id)
    {
        $column = $this->columnModel->find($id);
        if (!$column) {
            return $this->response->setJSON(['success' => false, 'message' => 'Column not found.']);
        }

        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $deleted = $this->columnModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Column deleted.' : 'Failed to delete column.',
        ]);
    }
}