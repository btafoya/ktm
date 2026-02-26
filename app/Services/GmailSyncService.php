<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoogleAuthService;
use App\Models\GmailSenderModel;
use App\Models\EmailModel;
use App\Models\BoardModel;
use App\Models\ColumnModel;
use App\Models\CardModel;
use App\Models\ChecklistItemModel;

class GmailSyncService
{
    private GoogleAuthService $authService;
    private GmailSenderModel $senderModel;
    private EmailModel $emailModel;
    private BoardModel $boardModel;
    private ColumnModel $columnModel;
    private CardModel $cardModel;
    private ChecklistItemModel $checklistModel;

    public function __construct()
    {
        $this->authService = new GoogleAuthService();
        $this->senderModel = new GmailSenderModel();
        $this->emailModel = new EmailModel();
        $this->boardModel = new BoardModel();
        $this->columnModel = new ColumnModel();
        $this->cardModel = new CardModel();
        $this->checklistModel = new ChecklistItemModel();
    }

    public function fetchEmails(int $userId): array
    {
        if (!$this->authService->hasValidToken($userId)) {
            return ['success' => false, 'message' => 'Google account not connected.'];
        }

        $token = $this->authService->getAccessToken($userId);

        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get access token.'];
        }

        $senders = $this->senderModel->getActiveForUser($userId);

        if (empty($senders)) {
            return ['success' => true, 'message' => 'No sender rules configured.'];
        }

        $created = 0;
        $attached = 0;

        foreach ($senders as $sender) {
            $result = $this->fetchEmailsForSender($userId, $token, $sender);
            $created += $result['created'] ?? 0;
            $attached += $result['attached'] ?? 0;
        }

        return [
            'success' => true,
            'message' => "Processed {$created} new emails, attached {$attached} to existing cards.",
            'created' => $created,
            'attached' => $attached,
        ];
    }

    private function fetchEmailsForSender(int $userId, string $token, array $sender): array
    {
        $query = 'from:' . $sender['email'];

        if (!empty($sender['keyword'])) {
            $query .= ' ' . $sender['keyword'];
        }

        $searchUrl = 'https://www.googleapis.com/gmail/v1/users/me/messages?q=' . urlencode($query) . '&maxResults=10';

        $ch = curl_init($searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 401) {
            $token = $this->authService->refreshAccessToken($userId);
            if (!$token) {
                return ['created' => 0, 'attached' => 0];
            }

            $ch = curl_init($searchUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

            $response = curl_exec($ch);
            curl_close($ch);
        }

        $data = json_decode($response, true);
        $messages = $data['messages'] ?? [];

        $created = 0;
        $attached = 0;

        foreach ($messages as $message) {
            $gmailMessageId = $message['id'];

            if ($this->emailModel->findByGmailId($gmailMessageId)) {
                continue;
            }

            $emailData = $this->fetchMessage($userId, $token, $gmailMessageId);

            if (!$emailData) {
                continue;
            }

            if ($sender['card_id']) {
                $this->attachEmailToCard($sender['card_id'], $emailData);
                $attached++;
            } elseif ($sender['column_id']) {
                $cardId = $this->createCardFromEmail($userId, $sender['column_id'], $emailData);
                if ($cardId) {
                    $created++;
                }
            }
        }

        return ['created' => $created, 'attached' => $attached];
    }

    private function fetchMessage(int $userId, string $token, string $messageId): ?array
    {
        $messageUrl = "https://www.googleapis.com/gmail/v1/users/me/messages/{$messageId}?format=full";

        $ch = curl_init($messageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 401) {
            $token = $this->authService->refreshAccessToken($userId);
            if (!$token) {
                return null;
            }

            $ch = curl_init($messageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

            $response = curl_exec($ch);
            curl_close($ch);
        }

        $messageData = json_decode($response, true);

        if (!isset($messageData['payload'])) {
            return null;
        }

        $headers = [];
        foreach ($messageData['payload']['headers'] ?? [] as $header) {
            $headers[$header['name']] = $header['value'];
        }

        $senderEmail = $headers['From'] ?? '';
        $senderName = '';
        if (preg_match('/(.*) <(.*)>/', $senderEmail, $matches)) {
            $senderName = trim($matches[1]);
            $senderEmail = trim($matches[2]);
        }

        $subject = $headers['Subject'] ?? '(No subject)';
        $snippet = $messageData['snippet'] ?? '';
        $threadId = $messageData['threadId'] ?? '';

        $body = $this->extractBody($messageData['payload']);

        return [
            'gmail_message_id' => $messageId,
            'thread_id' => $threadId,
            'sender_email' => $senderEmail,
            'sender_name' => $senderName,
            'subject' => $subject,
            'snippet' => $snippet,
            'body' => $body,
            'received_at' => date('Y-m-d H:i:s', ($messageData['internalDate'] ?? 0) / 1000),
        ];
    }

    private function extractBody(array $payload): string
    {
        if (isset($payload['body']['data'])) {
            return $this->decodeBase64($payload['body']['data']);
        }

        foreach ($payload['parts'] ?? [] as $part) {
            if (isset($part['mimeType']) && $part['mimeType'] === 'text/html') {
                if (isset($part['body']['data'])) {
                    return $this->decodeBase64($part['body']['data']);
                }
            }

            if (isset($part['parts'])) {
                $body = $this->extractBody($part);
                if (!empty($body)) {
                    return $body;
                }
            }
        }

        return '';
    }

    private function decodeBase64(string $data): string
    {
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        return base64_decode($data);
    }

    private function createCardFromEmail(int $userId, int $columnId, array $emailData): ?int
    {
        $column = $this->columnModel->find($columnId);

        if (!$column) {
            return null;
        }

        $title = $emailData['subject'];
        $description = "**From:** {$emailData['sender_name']} <{$emailData['sender_email']}>\n\n" .
                       $emailData['snippet'];

        $cardId = $this->cardModel->insert([
            'column_id' => $columnId,
            'title' => $title,
            'description' => $description,
            'position' => $this->getNextPosition($columnId),
            'is_email' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($cardId) {
            $this->emailModel->insert([
                'card_id' => $cardId,
                'gmail_message_id' => $emailData['gmail_message_id'],
                'thread_id' => $emailData['thread_id'],
                'sender_email' => $emailData['sender_email'],
                'sender_name' => $emailData['sender_name'],
                'subject' => $emailData['subject'],
                'snippet' => $emailData['snippet'],
                'body' => $emailData['body'],
                'received_at' => $emailData['received_at'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $cardId;
    }

    private function attachEmailToCard(int $cardId, array $emailData): void
    {
        $this->emailModel->insert([
            'card_id' => $cardId,
            'gmail_message_id' => $emailData['gmail_message_id'],
            'thread_id' => $emailData['thread_id'],
            'sender_email' => $emailData['sender_email'],
            'sender_name' => $emailData['sender_name'],
            'subject' => $emailData['subject'],
            'snippet' => $emailData['snippet'],
            'body' => $emailData['body'],
            'received_at' => $emailData['received_at'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getGmailUrl(string $messageId): string
    {
        return "https://mail.google.com/mail/u/0/#inbox/{$messageId}";
    }

    private function getNextPosition(int $columnId): int
    {
        $lastCard = $this->cardModel
            ->where('column_id', $columnId)
            ->orderBy('position', 'DESC')
            ->first();

        return ($lastCard['position'] ?? 0) + 1;
    }

    public function setWatch(int $userId): ?array
    {
        if (!$this->authService->hasValidToken($userId)) {
            return null;
        }

        $token = $this->authService->getAccessToken($userId);

        if (!$token) {
            return null;
        }

        $webhookUrl = getenv('app.baseURL') . '/gmail/webhook';
        $secret = getenv('gmail.webhook.secret');

        $watchData = [
            'topicName' => getenv('gmail.pubsub.topic'),
            'labelIds' => ['INBOX'],
        ];

        if ($webhookUrl) {
            $watchData['labelIds'] = ['INBOX'];
        }

        $watchUrl = 'https://www.googleapis.com/gmail/v1/users/me/watch';

        $ch = curl_init($watchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($watchData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json",
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function stopWatch(int $userId): bool
    {
        $token = $this->authService->getAccessToken($userId);

        if (!$token) {
            return false;
        }

        $stopUrl = 'https://www.googleapis.com/gmail/v1/users/me/stop';

        $ch = curl_init($stopUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

        curl_exec($ch);
        curl_close($ch);

        return true;
    }
}