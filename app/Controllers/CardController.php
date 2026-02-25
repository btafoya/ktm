<?php

namespace App\Controllers;

use App\Models\CardModel;
use App\Models\ColumnModel;
use App\Models\BoardModel;
use App\Models\ChecklistItemModel;
use App\Models\TagModel;
use CodeIgniter\HTTP\ResponseInterface;

class CardController extends BaseController
{
    protected CardModel $cardModel;
    protected ColumnModel $columnModel;
    protected BoardModel $boardModel;
    protected ChecklistItemModel $checklistModel;
    protected TagModel $tagModel;

    public function __construct()
    {
        $this->cardModel = model(CardModel::class);
        $this->columnModel = model(ColumnModel::class);
        $this->boardModel = model(BoardModel::class);
        $this->checklistModel = model(ChecklistItemModel::class);
        $this->tagModel = model(TagModel::class);
    }

    /**
     * Store new card
     */
    public function store(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $columnId = (int) $this->request->getPost('column_id');

        $column = $this->columnModel->find($columnId);

        if (!$column) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Column not found'], 404);
        }

        $board = $this->boardModel->find($column['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        if (!$this->validate([
            'column_id' => 'required|integer',
            'board_id' => 'required|integer',
            'title' => 'required|string|max_length[255]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ], 422);
        }

        $position = $this->cardModel->getMaxPosition($columnId) + 1;

        $cardData = [
            'column_id' => $columnId,
            'board_id' => $this->request->getPost('board_id'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description') ?: null,
            'color' => $this->request->getPost('color') ?: '#6c757d',
            'priority' => $this->request->getPost('priority') ?: 'medium',
            'due_date' => $this->request->getPost('due_date') ?: null,
            'position' => $position,
        ];

        $cardId = $this->cardModel->insert($cardData);

        if (!$cardId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create card',
            ], 500);
        }

        // Handle tags
        $tags = $this->request->getPost('tags');
        if (is_array($tags)) {
            foreach ($tags as $tagName) {
                $tag = $this->tagModel->getOrCreate($tagName);
                $this->tagModel->attachToCard($cardId, $tag['id']);
            }
        }

        // Handle checklist
        $checklistItems = $this->request->getPost('checklist');
        if (is_array($checklistItems)) {
            foreach ($checklistItems as $index => $text) {
                if (trim($text)) {
                    $this->checklistModel->insert([
                        'card_id' => $cardId,
                        'text' => trim($text),
                        'position' => $index,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $card = $this->cardModel->getWithRelations($cardId);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Card created',
            'card' => $card,
        ]);
    }

    /**
     * Show card details
     */
    public function show(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $card = $this->cardModel->getWithRelations($id);

        return $this->response->setJSON([
            'status' => 'success',
            'card' => $card,
        ]);
    }

    /**
     * Update card
     */
    public function update(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $data = $this->request->getRawInput();

        // Validate required fields
        if (isset($data['title']) && empty($data['title'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Title is required',
            ], 422);
        }

        $allowedFields = ['title', 'description', 'color', 'priority', 'due_date'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (!$this->cardModel->update($id, $updateData)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update card',
            ], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Card updated',
        ]);
    }

    /**
     * Delete card
     */
    public function delete(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        // Delete attachments
        model('App\Models\AttachmentModel')->deleteForCard($id);

        if (!$this->cardModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete card',
            ], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Card deleted',
        ]);
    }

    /**
     * Move card to another column
     */
    public function move(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['target_column_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Target column ID required',
            ], 422);
        }

        $targetColumn = $this->columnModel->find($data['target_column_id']);

        if (!$targetColumn || $targetColumn['board_id'] !== $card['board_id']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid target column'], 422);
        }

        $newPosition = $data['position'] ?? $this->cardModel->getMaxPosition($data['target_column_id']) + 1;

        if (!$this->cardModel->moveToColumn($id, $data['target_column_id'], $newPosition)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to move card'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Card moved',
        ]);
    }

    /**
     * Reorder card in column
     */
    public function reorder(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['position'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Position required'], 422);
        }

        if (!$this->cardModel->update($id, ['position' => $data['position']])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to reorder card'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Card reordered',
        ]);
    }

    /**
     * Add tag to card
     */
    public function addTag(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $tagName = $this->request->getJSON(true)['tag_name'] ?? '';

        if (empty($tagName)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tag name required'], 422);
        }

        $tag = $this->tagModel->getOrCreate($tagName);

        if (!$this->tagModel->attachToCard($id, $tag['id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add tag'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tag added',
            'tag' => $tag,
        ]);
    }

    /**
     * Remove tag from card
     */
    public function removeTag(int $id, int $tagId): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        if (!$this->tagModel->detachFromCard($id, $tagId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to remove tag'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tag removed',
        ]);
    }

    /**
     * Upload attachment
     */
    public function uploadAttachment(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $card = $this->cardModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $file = $this->request->getFile('attachment');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid file'], 422);
        }

        // Max 10MB
        if ($file->getSize() > 10 * 1024 * 1024) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File too large (max 10MB)'], 422);
        }

        // Allowed types
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ];

        if (!in_array($file->getClientMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid file type'], 422);
        }

        // Generate unique filename
        $filename = bin2hex(random_bytes(8)) . '.' . $file->getExtension();

        // Store file
        $uploadPath = WRITEPATH . 'uploads/attachments/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);

        // Save to database
        $attachmentModel = model('App\Models\AttachmentModel');

        $attachmentId = $attachmentModel->insert([
            'card_id' => $id,
            'filename' => $filename,
            'original_name' => $file->getClientName(),
            'filesize' => $file->getSize(),
            'mimetype' => $file->getClientMimeType(),
            'stored_at' => 'local',
            'file_path' => 'attachments/' . $filename,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'File uploaded',
            'attachment' => $attachmentModel->find($attachmentId),
        ]);
    }
}