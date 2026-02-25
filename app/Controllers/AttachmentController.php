<?php

namespace App\Controllers;

use App\Models\AttachmentModel;
use App\Models\CardModel;
use App\Models\BoardModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\DownloadResponse;

class AttachmentController extends BaseController
{
    protected AttachmentModel $attachmentModel;
    protected CardModel $cardModel;
    protected BoardModel $boardModel;

    public function __construct()
    {
        $this->attachmentModel = model(AttachmentModel::class);
        $this->cardModel = model(CardModel::class);
        $this->boardModel = model(BoardModel::class);
    }

    /**
     * Delete attachment
     */
    public function delete(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $attachment = $this->attachmentModel->find($id);

        if (!$attachment) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Attachment not found'], 404);
        }

        $card = $this->cardModel->find($attachment['card_id']);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        if (!$this->attachmentModel->deleteWithFile($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete attachment'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Attachment deleted',
        ]);
    }

    /**
     * Download attachment
     */
    public function download(int $id): DownloadResponse|ResponseInterface
    {
        $userId = session()->get('user_id');
        $attachment = $this->attachmentModel->find($id);

        if (!$attachment) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Attachment not found'], 404);
        }

        $card = $this->cardModel->find($attachment['card_id']);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        $board = $this->boardModel->find($card['board_id']);

        if (!$board || $board['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        if ($attachment['stored_at'] !== 'local') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unsupported storage'], 422);
        }

        $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];

        if (!is_file($filePath)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File not found'], 404);
        }

        return $this->response->download($filePath, null)->setFileName($attachment['original_name']);
    }
}