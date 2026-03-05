<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['form', 'url', 'session'];

        parent::initController($request, $response, $logger);
    }

    /**
     * Handle exceptions and return appropriate response
     *
     * @param \Throwable $e
     * @return ResponseInterface
     */
    protected function handleException(\Throwable $e): ResponseInterface
    {
        $logger = \Config\Services::logger();

        // Log the error
        $logger->error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => current_url(),
            'user_id' => session()->get('user_id'),
            'timestamp' => Time::now()->toDateTimeString()
        ]);

        // Return JSON for API requests
        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => ENVIRONMENT === 'production' ? 'An error occurred. Please try again.' : $e->getMessage(),
                'error' => ENVIRONMENT === 'production' ? null : [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }

        // Return HTML error page for web requests
        if (ENVIRONMENT === 'production') {
            return $this->response->setStatusCode(500)->setBody(view('errors/html/production'));
        }

        // Show detailed error in development
        $data = [
            'title' => '500 - Internal Server Error',
            'exception' => $e,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];

        return $this->response->setStatusCode(500)->setBody(view('errors/html/production', $data));
    }
}
