<?php

namespace App\Controllers;

use App\Models\CardModel;
use App\Models\ColumnModel;
use App\Models\BoardModel;

class CardController extends BaseController
{
    protected $cardModel;
    protected $columnModel;
    protected $boardModel;

    public function __construct()
    {
        $this->cardModel = new CardModel();
        $this->columnModel = new ColumnModel();
        $this->boardModel = new BoardModel();
    }

    public function show($id)
    {
        $card = $this->cardModel->getWithDetails($id);
        if (!$card) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Card not found.']);
            }
            return redirect()->back()->with('error', 'Card not found.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true] + $card);
        }

        return view('cards/show', ['card' => $card]);
    }

    public function create()
    {
        $rules = [
            'column_id' => 'required|numeric',
            'title' => 'required|min_length[1]|max_length[200]',
            'description' => 'permit_empty',
            'priority' => 'permit_empty|in_list[low,medium,high]',
            'due_date' => 'permit_empty|valid_date[Y-m-d\TH:i]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $column = $this->columnModel->find($this->request->getPost('column_id'));
        if (!$column) {
            return $this->response->setJSON(['success' => false, 'message' => 'Column not found.']);
        }

        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $maxPosition = $this->cardModel->where('column_id', $column['id'])->selectMax('position')->first()['position'] ?? -1;

        $cardData = [
            'column_id' => $column['id'],
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description') ?: '',
            'priority' => $this->request->getPost('priority') ?: 'low',
            'due_date' => $this->request->getPost('due_date') ?: null,
            'position' => $maxPosition + 1,
            'is_completed' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $cardId = $this->cardModel->insert($cardData);

        if ($cardId) {
            $card = $this->cardModel->find($cardId);
            $card['column_name'] = $column['name'];
            return $this->response->setJSON(['success' => true, 'card' => $card]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create card.']);
    }

    public function update($id)
    {
        $card = $this->cardModel->find($id);
        if (!$card) {
            return $this->response->setJSON(['success' => false, 'message' => 'Card not found.']);
        }

        $column = $this->columnModel->find($card['column_id']);
        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $rules = [
            'title' => 'permit_empty|min_length[1]|max_length[200]',
            'description' => 'permit_empty',
            'priority' => 'permit_empty|in_list[low,medium,high]',
            'due_date' => 'permit_empty|valid_date[Y-m-d\TH:i]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];

        $title = $this->request->getPost('title');
        if ($title !== null && $title !== '') {
            $updateData['title'] = $title;
        }

        $description = $this->request->getPost('description');
        if ($description !== null) {
            $updateData['description'] = $description;
        }

        $priority = $this->request->getPost('priority');
        if ($priority !== null && in_array($priority, ['low', 'medium', 'high'])) {
            $updateData['priority'] = $priority;
        }

        $dueDate = $this->request->getPost('due_date');
        if ($dueDate !== null && $dueDate !== '') {
            $updateData['due_date'] = $dueDate;
        }

        $isCompleted = $this->request->getPost('is_completed');
        if ($isCompleted !== null) {
            $updateData['is_completed'] = filter_var($isCompleted, FILTER_VALIDATE_BOOLEAN);
        }

        $updated = $this->cardModel->update($id, $updateData);

        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Card updated.' : 'Failed to update card.',
        ]);
    }

    public function delete($id)
    {
        $card = $this->cardModel->find($id);
        if (!$card) {
            return $this->response->setJSON(['success' => false, 'message' => 'Card not found.']);
        }

        $column = $this->columnModel->find($card['column_id']);
        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $deleted = $this->cardModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Card deleted.' : 'Failed to delete card.',
        ]);
    }

    public function move()
    {
        $cardId = $this->request->getPost('card_id');
        $targetColumnId = $this->request->getPost('column_id');
        $cardIdsString = $this->request->getPost('card_ids');
        $cardIds = $cardIdsString ? json_decode($cardIdsString, true) : [];

        if (!$cardId || !$targetColumnId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required parameters.']);
        }

        $card = $this->cardModel->find($cardId);
        if (!$card) {
            return $this->response->setJSON(['success' => false, 'message' => 'Card not found.']);
        }

        $column = $this->columnModel->find($card['column_id']);
        $targetColumn = $this->columnModel->find($targetColumnId);

        if (!$column || !$targetColumn) {
            return $this->response->setJSON(['success' => false, 'message' => 'Column not found.']);
        }

        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        if (!$board || $board['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        if ($column['board_id'] !== $targetColumn['board_id']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot move cards between boards.']);
        }

        $success = $this->cardModel->reorder($targetColumnId, $cardIds);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Cards moved.' : 'Failed to move cards.',
        ]);
    }
}