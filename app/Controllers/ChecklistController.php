<?php

namespace App\Controllers;

use App\Models\ChecklistItemModel;
use App\Models\CardModel;
use App\Models\ColumnModel;
use App\Models\BoardModel;

class ChecklistController extends BaseController
{
    protected $checklistModel;
    protected $cardModel;
    protected $columnModel;
    protected $boardModel;

    public function __construct()
    {
        $this->checklistModel = new ChecklistItemModel();
        $this->cardModel = new CardModel();
        $this->columnModel = new ColumnModel();
        $this->boardModel = new BoardModel();
    }

    private function checkCardAccess(int $cardId): bool
    {
        $card = $this->cardModel->find($cardId);
        if (!$card) {
            return false;
        }

        $column = $this->columnModel->find($card['column_id']);
        $board = $this->boardModel->find($column['board_id']);
        $userId = session()->get('user_id');

        return $board && $board['user_id'] == $userId;
    }

    public function create()
    {
        $rules = [
            'card_id' => 'required|numeric',
            'title' => 'required|min_length[1]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $cardId = $this->request->getPost('card_id');

        if (!$this->checkCardAccess($cardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $maxPosition = $this->checklistModel->where('card_id', $cardId)->selectMax('position')->first()['position'] ?? -1;

        $itemId = $this->checklistModel->insert([
            'card_id' => $cardId,
            'title' => $this->request->getPost('title'),
            'is_completed' => false,
            'position' => $maxPosition + 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($itemId) {
            $item = $this->checklistModel->find($itemId);
            return $this->response->setJSON(['success' => true, 'item' => $item]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create checklist item.']);
    }

    public function toggle($id)
    {
        $item = $this->checklistModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found.']);
        }

        if (!$this->checkCardAccess($item['card_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $success = $this->checklistModel->toggleComplete($id);

        if ($success) {
            $item = $this->checklistModel->find($id);
            return $this->response->setJSON(['success' => true, 'item' => $item]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update item.']);
    }

    public function update($id)
    {
        $item = $this->checklistModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found.']);
        }

        if (!$this->checkCardAccess($item['card_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $rules = ['title' => 'required|min_length[1]|max_length[200]'];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $updated = $this->checklistModel->update($id, [
            'title' => $this->request->getPost('title'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Item updated.' : 'Failed to update item.',
        ]);
    }

    public function delete($id)
    {
        $item = $this->checklistModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found.']);
        }

        if (!$this->checkCardAccess($item['card_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $deleted = $this->checklistModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Item deleted.' : 'Failed to delete item.',
        ]);
    }

    public function reorder($cardId)
    {
        if (!$this->checkCardAccess($cardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $itemIds = $this->request->getJSON(true)['item_ids'] ?? [];

        $success = $this->checklistModel->reorder($cardId, $itemIds);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Items reordered.' : 'Failed to reorder items.',
        ]);
    }
}