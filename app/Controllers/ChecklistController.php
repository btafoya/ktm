<?php

namespace App\Controllers;

use App\Models\ChecklistItemModel;
use App\Models\CardModel;
use App\Models\BoardModel;
use CodeIgniter\HTTP\ResponseInterface;

class ChecklistController extends BaseController
{
    protected ChecklistItemModel $checklistModel;
    protected CardModel $cardModel;
    protected BoardModel $boardModel;

    public function __construct()
    {
        $this->checklistModel = model(ChecklistItemModel::class);
        $this->cardModel = model(CardModel::class);
        $this->boardModel = model(BoardModel::class);
    }

    /**
     * Store checklist item
     */
    public function store(int $cardId): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($cardId);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $text = $this->request->getJSON(true)['text'] ?? '';

        if (empty($text)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Text is required'], 422);
        }

        $position = count($this->checklistModel->getForCard($cardId));

        $itemId = $this->checklistModel->insert([
            'card_id' => $cardId,
            'text' => $text,
            'position' => $position,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Checklist item added',
            'item' => $this->checklistModel->find($itemId),
            'progress' => $this->checklistModel->getProgress($cardId),
        ]);
    }

    /**
     * Update checklist item
     */
    public function update(int $id): ResponseInterface
    {
        $item = $this->checklistModel->find($id);

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        $card = $this->cardModel->find($item['card_id']);
        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== session()->get('user_id')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $data = $this->request->getRawInput();

        $updateData = [];

        if (isset($data['text'])) {
            $updateData['text'] = $data['text'];
        }

        if (isset($data['completed'])) {
            $updateData['completed'] = (bool) $data['completed'];
        }

        if (isset($data['position'])) {
            $updateData['position'] = (int) $data['position'];
        }

        if (!$this->checklistModel->update($id, $updateData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update item'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Item updated',
            'progress' => $this->checklistModel->getProgress($item['card_id']),
        ]);
    }

    /**
     * Delete checklist item
     */
    public function delete(int $id): ResponseInterface
    {
        $item = $this->checklistModel->find($id);

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        $card = $this->cardModel->find($item['card_id']);
        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== session()->get('user_id')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $cardId = $item['card_id'];

        if (!$this->checklistModel->delete($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete item'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Item deleted',
            'progress' => $this->checklistModel->getProgress($cardId),
        ]);
    }

    /**
     * Toggle item completion
     */
    public function toggle(int $id): ResponseInterface
    {
        $item = $this->checklistModel->find($id);

        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        $card = $this->cardModel->find($item['card_id']);
        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== session()->get('user_id')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $this->checklistModel->toggle($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Item toggled',
            'item' => $this->checklistModel->find($id),
            'progress' => $this->checklistModel->getProgress($item['card_id']),
        ]);
    }
}