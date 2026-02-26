<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $request->getJSON()
                ? $this->unauthorizedResponse('Unauthorized. Please log in.')
                : redirect()->to('auth/login');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = service('response');
        $response->setStatusCode(401);
        $response->setJSON(['error' => $message]);
        return $response;
    }
}