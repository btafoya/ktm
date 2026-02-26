<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    private int $maxRequests = 60;
    private int $windowSeconds = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        $key = $this->getRateLimitKey($request);
        $cache = cache();

        $current = (int) $cache->get($key);

        if ($current >= $this->maxRequests) {
            $response = service('response');
            $response->setStatusCode(429);
            $response->setHeader('Retry-After', (string) $this->windowSeconds);
            if ($request->isAJAX() || $request->isCLI()) {
                return $response->setJSON(['error' => 'Too many requests. Please try again later.']);
            }
            return $response->setBody('Too many requests. Please try again later.');
        }

        $cache->save($key, $current + 1, $this->windowSeconds);

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('X-RateLimit-Limit', (string) $this->maxRequests);
        $response->setHeader('X-RateLimit-Window', (string) $this->windowSeconds);
        return $response;
    }

    private function getRateLimitKey(RequestInterface $request): string
    {
        $ip = $request->getIPAddress();
        $uri = $request->getUri()->getPath();
        // Replace reserved cache characters: {}()/\@:
        $safeUri = str_replace(['{', '}', '(', ')', '/', '\\', '@', ':'], '_', $uri);
        return "rate_limit_{$ip}_{$safeUri}";
    }
}