<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentModel extends Model
{
    protected $table            = 'attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'card_id',
        'filename',
        'original_name',
        'filesize',
        'mimetype',
        'stored_at',
        'file_path',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'card_id'       => 'required|integer',
        'filename'      => 'required|string|max_length[255]',
        'original_name' => 'required|string|max_length[255]',
        'filesize'      => 'required|integer',
        'mimetype'      => 'required|string|max_length[100]',
        'stored_at'     => 'required|in_list[local,s3]',
        'file_path'     => 'required|string|max_length[500]',
    ];
    protected $skipValidation = false;

    /**
     * Get attachments for a card
     */
    public function getForCard(int $cardId): array
    {
        return $this->where('card_id', $cardId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get total file size for a card
     */
    public function getTotalSize(int $cardId): int
    {
        $result = $this->selectSum('filesize')
            ->where('card_id', $cardId)
            ->first();

        return (int)($result['filesize'] ?? 0);
    }

    /**
     * Delete file and record
     */
    public function deleteWithFile(int $id): bool
    {
        $attachment = $this->find($id);

        if (!$attachment) {
            return false;
        }

        // Delete physical file
        if ($attachment['stored_at'] === 'local') {
            $filePath = WRITEPATH . 'uploads/' . $attachment['file_path'];

            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        return $this->delete($id);
    }

    /**
     * Delete all attachments for a card
     */
    public function deleteForCard(int $cardId): bool
    {
        $attachments = $this->getForCard($cardId);

        foreach ($attachments as $attachment) {
            $this->deleteWithFile($attachment['id']);
        }

        return true;
    }
}