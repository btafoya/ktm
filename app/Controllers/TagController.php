<?php

namespace App\Controllers;

use App\Models\TagModel;
use CodeIgniter\HTTP\ResponseInterface;

class TagController extends BaseController
{
    protected TagModel $tagModel;

    public function __construct()
    {
        $this->tagModel = model(TagModel::class);
    }

    /**
     * List all tags
     */
    public function index(): ResponseInterface
    {
        return $this->response->setJSON([
            'status' => 'success',
            'tags' => $this->tagModel->getAll(),
        ]);
    }

    /**
     * Create new tag
     */
    public function store(): ResponseInterface
    {
        if (!$this->validate([
            'name' => 'required|string|max_length[50]|is_unique[tags.name]',
            'color' => 'permit_empty|string|max_length[7]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
            ], 422);
        }

        $tagId = $this->tagModel->insert([
            'name' => $this->request->getPost('name'),
            'color' => $this->request->getPost('color') ?: '#6c757d',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tag created',
            'tag' => $this->tagModel->find($tagId),
        ]);
    }

    /**
     * Update tag
     */
    public function update(int $id): ResponseInterface
    {
        $tag = $this->tagModel->find($id);

        if (!$tag) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tag not found'], 404);
        }

        $data = $this->request->getRawInput();

        if (isset($data['name']) && $data['name'] !== $tag['name']) {
            if ($this->tagModel->where('name', $data['name'])->first()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tag name already exists',
                ], 422);
            }
        }

        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['color'])) {
            $updateData['color'] = $data['color'];
        }

        if (!$this->tagModel->update($id, $updateData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update tag'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tag updated',
            'tag' => $this->tagModel->find($id),
        ]);
    }

    /**
     * Delete tag
     */
    public function delete(int $id): ResponseInterface
    {
        $tag = $this->tagModel->find($id);

        if (!$tag) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tag not found'], 404);
        }

        if (!$this->tagModel->delete($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete tag'], 500);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Tag deleted',
        ]);
    }
}