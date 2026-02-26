<?php

namespace App\Controllers;

use App\Models\GmailSenderModel;
use App\Models\BoardModel;
use App\Models\ColumnModel;

class GmailController extends BaseController
{
    protected $gmailSenderModel;
    protected $boardModel;
    protected $columnModel;

    public function __construct()
    {
        $this->gmailSenderModel = new GmailSenderModel();
        $this->boardModel = new BoardModel();
        $this->columnModel = new ColumnModel();
    }

    public function senders()
    {
        $userId = session()->get('user_id');
        $senders = $this->gmailSenderModel->getForUser($userId);

        return $this->response->setJSON(['senders' => $senders]);
    }

    public function createSender()
    {
        $rules = [
            'email' => 'required|valid_email',
            'name' => 'permit_empty|max_length[100]',
            'card_id' => 'permit_empty|numeric',
            'column_id' => 'permit_empty|numeric',
            'keyword' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $userId = session()->get('user_id');
        $cardId = $this->request->getPost('card_id');
        $columnId = $this->request->getPost('column_id');

        if ($cardId) {
            $cardModel = new \App\Models\CardModel();
            $card = $cardModel->find($cardId);
            if (!$card) {
                return $this->response->setJSON(['success' => false, 'message' => 'Card not found.']);
            }
        }

        if ($columnId) {
            $column = $this->columnModel->find($columnId);
            if (!$column || $column['board_id'] != $userId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid column.']);
            }
        }

        $senderId = $this->gmailSenderModel->insert([
            'user_id' => $userId,
            'email' => $this->request->getPost('email'),
            'name' => $this->request->getPost('name'),
            'card_id' => $cardId,
            'column_id' => $columnId,
            'keyword' => $this->request->getPost('keyword'),
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($senderId) {
            $sender = $this->gmailSenderModel->find($senderId);
            return $this->response->setJSON(['success' => true, 'sender' => $sender]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create sender rule.']);
    }

    public function updateSender($id)
    {
        $sender = $this->gmailSenderModel->find($id);
        if (!$sender) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sender rule not found.']);
        }

        $userId = session()->get('user_id');
        if ($sender['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $rules = [
            'email' => 'permit_empty|valid_email',
            'name' => 'permit_empty|max_length[100]',
            'keyword' => 'permit_empty|max_length[100]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];

        $email = $this->request->getPost('email');
        if ($email !== null && $email !== '') {
            $updateData['email'] = $email;
        }

        $name = $this->request->getPost('name');
        if ($name !== null) {
            $updateData['name'] = $name;
        }

        $keyword = $this->request->getPost('keyword');
        if ($keyword !== null) {
            $updateData['keyword'] = $keyword;
        }

        $isActive = $this->request->getPost('is_active');
        if ($isActive !== null) {
            $updateData['is_active'] = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);
        }

        $updated = $this->gmailSenderModel->update($id, $updateData);

        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Sender rule updated.' : 'Failed to update sender rule.',
        ]);
    }

    public function deleteSender($id)
    {
        $sender = $this->gmailSenderModel->find($id);
        if (!$sender) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sender rule not found.']);
        }

        $userId = session()->get('user_id');
        if ($sender['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $deleted = $this->gmailSenderModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Sender rule deleted.' : 'Failed to delete sender rule.',
        ]);
    }

    public function testWebhook()
    {
        $secret = getenv('gmail.webhook.secret');
        $receivedSecret = $this->request->getHeaderLine('X-Goog-Channel-Token');

        if ($secret && $receivedSecret !== $secret) {
            log_message('error', 'Invalid Gmail webhook secret');
            return $this->response->setStatusCode(403);
        }

        $messageData = $this->request->getJSON(true);
        $emailAddress = $messageData['emailAddress'] ?? '';

        log_message('info', "Gmail webhook received from: {$emailAddress}");

        return $this->response->setJSON(['status' => 'received']);
    }
}