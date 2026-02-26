<?php

namespace App\Controllers;

use App\Models\TagModel;
use App\Models\CardModel;
use App\Models\ColumnModel;
use App\Models\BoardModel;

class TagController extends BaseController
{
    protected $tagModel;
    protected $cardModel;
    protected $columnModel;
    protected $boardModel;

    public function __construct()
    {
        $this->tagModel = new TagModel();
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

    public function index()
    {
        $userId = session()->get('user_id');
        $tags = $this->tagModel->getForUser($userId);

        return $this->response->setJSON(['tags' => $tags]);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[1]|max_length[50]',
            'color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $userId = session()->get('user_id');
        $tagId = $this->tagModel->insert([
            'user_id' => $userId,
            'name' => $this->request->getPost('name'),
            'color' => $this->request->getPost('color') ?: '#0d6efd',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($tagId) {
            $tag = $this->tagModel->find($tagId);
            return $this->response->setJSON(['success' => true, 'tag' => $tag]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create tag.']);
    }

    public function update($id)
    {
        $tag = $this->tagModel->find($id);
        if (!$tag) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tag not found.']);
        }

        $userId = session()->get('user_id');
        if ($tag['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $rules = [
            'name' => 'permit_empty|min_length[1]|max_length[50]',
            'color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];

        $name = $this->request->getPost('name');
        if ($name !== null && $name !== '') {
            $updateData['name'] = $name;
        }

        $color = $this->request->getPost('color');
        if ($color !== null && $color !== '') {
            $updateData['color'] = $color;
        }

        $updated = $this->tagModel->update($id, $updateData);

        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Tag updated.' : 'Failed to update tag.',
        ]);
    }

    public function delete($id)
    {
        $tag = $this->tagModel->find($id);
        if (!$tag) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tag not found.']);
        }

        $userId = session()->get('user_id');
        if ($tag['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $deleted = $this->tagModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Tag deleted.' : 'Failed to delete tag.',
        ]);
    }

    public function addToCard($cardId, $tagId)
    {
        if (!$this->checkCardAccess($cardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $tag = $this->tagModel->find($tagId);
        if (!$tag) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tag not found.']);
        }

        $userId = session()->get('user_id');
        if ($tag['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $success = $this->tagModel->addTagToCard($cardId, $tagId);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Tag added to card.' : 'Failed to add tag to card.',
        ]);
    }

    public function removeFromCard($cardId, $tagId)
    {
        if (!$this->checkCardAccess($cardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $success = $this->tagModel->removeTagFromCard($cardId, $tagId);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Tag removed from card.' : 'Failed to remove tag from card.',
        ]);
    }

    public function updateCardTags($cardId)
    {
        if (!$this->checkCardAccess($cardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $tagIds = $this->request->getJSON(true)['tag_ids'] ?? [];

        $success = $this->tagModel->updateCardTags($cardId, $tagIds);

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Card tags updated.' : 'Failed to update card tags.',
        ]);
    }
}