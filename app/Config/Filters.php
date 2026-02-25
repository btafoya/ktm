<?php

namespace Config;

use App\Filters\ApiAuthFilter;
use App\Filters\AuthFilter;
use App\Filters\RateLimitFilter;
use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'        => CSRF::class,
        'toolbar'     => DebugToolbar::class,
        'honeypot'    => Honeypot::class,
        'invalidchars'=> InvalidChars::class,
        'secureheaders'=> SecureHeaders::class,
        'cors'        => Cors::class,
        'pagecache'   => PageCache::class,
        'performance' => PerformanceMetrics::class,
        'auth'        => AuthFilter::class,
        'api-auth'    => ApiAuthFilter::class,
        'ratelimit'   => RateLimitFilter::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [],
        'after' => [
            'toolbar',
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [],
        'after' => [],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * @var array<string, list<string>>
     */
    public array $methods = [
        'post' => ['csrf'],
        'put' => ['csrf'],
        'patch' => ['csrf'],
        'delete' => ['csrf'],
    ];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'ratelimit' => ['before' => ['auth/login', 'auth/register', 'api/auth/*']],
    ];
}