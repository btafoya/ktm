<?php

namespace App\Controllers;

use App\Models\AttachmentModel;
use App\Models\CardModel;
use App\Models\ColumnModel;
use App\Models\BoardModel;

class AttachmentController extends BaseController
{
    protected $attachmentModel;
    protected $cardModel;
    protected $columnModel;
    protected $boardModel;

    public function __construct()
    {
        $this->attachmentModel = new AttachmentModel();
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

    public function upload($cardId)
    {
        if (!$this->checkCardAccess($cardId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'No valid file uploaded.']);
        }

        $maxSize = 10 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON(['success' => false, 'message' => 'File size exceeds 10MB limit.']);
        }

        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain', 'text/markdown',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (!in_array($file->getClientMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'File type not allowed.']);
        }

        $uploadDir = ROOTPATH . 'writable/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $cardDir = $uploadDir . '/' . $cardId;
        if (!is_dir($cardDir)) {
            mkdir($cardDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($cardDir, $newName);

        $filePath = "uploads/{$cardId}/{$newName}";

        $attachmentId = $this->attachmentModel->insert([
            'card_id' => $cardId,
            'file_name' => $file->getClientName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($attachmentId) {
            $attachment = $this->attachmentModel->find($attachmentId);
            return $this->response->setJSON(['success' => true, 'attachment' => $attachment]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to save attachment.']);
    }

    public function download($id)
    {
        $attachment = $this->attachmentModel->find($id);
        if (!$attachment) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }

        if (!$this->checkCardAccess($attachment['card_id'])) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $filePath = ROOTPATH . 'writable/' . $attachment['file_path'];
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($filePath, null);
    }

    public function delete($id)
    {
        $attachment = $this->attachmentModel->find($id);
        if (!$attachment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attachment not found.']);
        }

        if (!$this->checkCardAccess($attachment['card_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $filePath = ROOTPATH . 'writable/' . $attachment['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $deleted = $this->attachmentModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Attachment deleted.' : 'Failed to delete attachment.',
        ]);
    }
}