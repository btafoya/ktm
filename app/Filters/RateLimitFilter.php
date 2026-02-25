<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    private const LIMIT = 5;
    private const WINDOW = 60; // 60 seconds

    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();
        $key = 'ratelimit_' . $ip;
        $cache = cache();

        $attempts = $cache->get($key) ?? 0;

        if ($attempts >= self::LIMIT) {
            return service('response')
                ->setJSON(['status' => 'error', 'message' => 'Too many attempts. Please try again later.'])
                ->setStatusCode(429);
        }

        $cache->save($key, $attempts + 1, self::WINDOW);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}