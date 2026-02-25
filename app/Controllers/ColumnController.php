<?php

namespace App\Controllers;

use App\Models\ColumnModel;
use App\Models\BoardModel;
use CodeIgniter\HTTP\ResponseInterface;

class ColumnController extends BaseController
{
    protected ColumnModel $columnModel;
    protected BoardModel $boardModel;

    public function __construct()
    {
        $this->columnModel = model(ColumnModel::class);
        $this->boardModel = model(BoardModel::class);
    }

    /**
     * Store new column
     */
    public function store(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $boardId = (int) $this->request->getPost('board_id');

        $board = $this->boardModel->find($boardId);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Board not found'], 404);
        }

        if (!$this->validate([
            'board_id' => 'required|integer',
            'name' => 'required|string|max_length[100]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ], 422);
        }

        $position = $this->columnModel->getMaxPosition($boardId) + 1;

        $columnId = $this->columnModel->insert([
            'board_id' => $boardId,
            'name' => $this->request->getPost('name'),
            'position' => $position,
        ]);

        if (!$columnId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create column',
            ], 500);
        }

        $column = $this->columnModel->find($columnId);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Column created',
            'column' => $column,
        ]);
    }

    /**
     * Update column
     */
    public function update(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $column = $this->columnModel->find($id);

        if (!$column) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Column not found'], 404);
        }

        $board = $this->boardModel->find($column['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        // Check if this is the last column
        if (!$this->columnModel->boardHasColumns($column['board_id']) ||
            count($this->columnModel->getForBoard($column['board_id'])) <= 1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Board must have at least one column',
            ], 422);
        }

        if (!$this->validate([
            'name' => 'required|string|max_length[100]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ], 422);
        }

        if (!$this->columnModel->update($id, [
            'name' => $this->request->getRawInputVar('name'),
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update column',
            ], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Column updated',
        ]);
    }

    /**
     * Delete column
     */
    public function delete(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $column = $this->columnModel->find($id);

        if (!$column) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Column not found'], 404);
        }

        $board = $this->boardModel->find($column['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        // Check if this is the last column
        if (count($this->columnModel->getForBoard($column['board_id'])) <= 1) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Board must have at least one column',
            ], 422);
        }

        if (!$this->columnModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete column',
            ], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Column deleted',
        ]);
    }

    /**
     * Reorder columns
     */
    public function reorder(): ResponseInterface
    {
        $columnIds = $this->request->getJSON(true)['column_ids'] ?? [];

        if (empty($columnIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No column IDs provided',
            ], 422);
        }

        $userId = session()->get('user_id');
        $columnModel = $this->columnModel;

        // Verify all columns belong to user's boards
        foreach ($columnIds as $columnId) {
            $column = $columnModel->find($columnId);
            if (!$column) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Column not found'], 404);
            }

            $board = $this->boardModel->find($column['board_id']);
            if (!$board || $board['user_id'] !== $userId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
            }
        }

        $this->columnModel->reorder($columnIds);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Columns reordered',
        ]);
    }
}