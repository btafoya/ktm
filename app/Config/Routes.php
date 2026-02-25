<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth routes
$routes->group('auth', static function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::attemptRegister');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::sendResetLink');
    $routes->get('reset-password/(:hash)', 'AuthController::resetPassword/$1');
    $routes->post('reset-password', 'AuthController::resetPasswordSubmit');
});

// Dashboard routes (require auth)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'BoardController::index', ['as' => 'dashboard']);

    // Boards
    $routes->group('boards', static function ($routes) {
        $routes->get('/', 'BoardController::index');
        $routes->get('create', 'BoardController::create');
        $routes->post('/', 'BoardController::store');
        $routes->get('(:num)', 'BoardController::show/$1');
        $routes->get('(:num)/edit', 'BoardController::edit/$1');
        $routes->put('(:num)', 'BoardController::update/$1');
        $routes->post('(:num)/archive', 'BoardController::archive/$1');
        $routes->delete('(:num)', 'BoardController::delete/$1');
    });

    // Columns
    $routes->group('columns', static function ($routes) {
        $routes->post('/', 'ColumnController::store');
        $routes->put('(:num)', 'ColumnController::update/$1');
        $routes->delete('(:num)', 'ColumnController::delete/$1');
        $routes->post('reorder', 'ColumnController::reorder');
    });

    // Cards
    $routes->group('cards', static function ($routes) {
        $routes->post('/', 'CardController::store');
        $routes->get('(:num)', 'CardController::show/$1');
        $routes->put('(:num)', 'CardController::update/$1');
        $routes->delete('(:num)', 'CardController::delete/$1');
        $routes->post('(:num)/move', 'CardController::move/$1');
        $routes->post('(:num)/reorder', 'CardController::reorder/$1');
        $routes->post('(:num)/tags', 'CardController::addTag/$1');
        $routes->delete('(:num)/tags/(:num)', 'CardController::removeTag/$1/$2');
        $routes->post('(:num)/upload', 'CardController::uploadAttachment/$1');
    });

    // Checklist
    $routes->group('checklist', static function ($routes) {
        $routes->post('(:num)', 'ChecklistController::store/$1');
        $routes->put('(:num)', 'ChecklistController::update/$1');
        $routes->delete('(:num)', 'ChecklistController::delete/$1');
        $routes->post('(:num)/toggle', 'ChecklistController::toggle/$1');
    });

    // Attachments
    $routes->group('attachments', static function ($routes) {
        $routes->delete('(:num)', 'AttachmentController::delete/$1');
        $routes->get('(:num)/download', 'AttachmentController::download/$1');
    });

    // Tags
    $routes->group('tags', static function ($routes) {
        $routes->get('/', 'TagController::index');
        $routes->post('/', 'TagController::store');
        $routes->put('(:num)', 'TagController::update/$1');
        $routes->delete('(:num)', 'TagController::delete/$1');
    });

    // User settings
    $routes->get('settings', 'UserController::settings');
    $routes->post('settings', 'UserController::updateSettings');
    $routes->get('profile', 'UserController::profile');
    $routes->post('profile', 'UserController::updateProfile');

    // Google integration
    $routes->get('google/auth', 'GoogleController::auth');
    $routes->get('google/callback', 'GoogleController::callback');
    $routes->get('google/calendars', 'GoogleController::listCalendars');
    $routes->post('google/calendars/select', 'GoogleController::selectCalendar');
    $routes->post('google/boards/(:num)/sync', 'GoogleController::syncBoard/$1');

    // Gmail integration
    $routes->post('gmail/senders', 'GmailController::addSender');
    $routes->get('gmail/senders', 'GmailController::listSenders');
    $routes->delete('gmail/senders/(:num)', 'GmailController::deleteSender/$1');
    $routes->post('gmail/watch', 'GmailController::watch');
    $routes->delete('gmail/watch', 'GmailController::unwatch');
    $routes->post('gmail/sync', 'GmailController::sync');
    $routes->post('gmail/webhook', 'GmailController::webhook');
});

// API routes (RESTful, require API auth)
$routes->group('api', ['filter' => 'api-auth'], static function ($routes) {
    // Boards
    $routes->resource('boards', ['controller' => 'Api\BoardController']);

    // Columns
    $routes->get('boards/(:num)/columns', 'Api\ColumnController::index/$1');
    $routes->post('boards/(:num)/columns', 'Api\ColumnController::store/$1');
    $routes->put('columns/(:num)', 'Api\ColumnController::update/$1');
    $routes->delete('columns/(:num)', 'Api\ColumnController::delete/$1');

    // Cards
    $routes->get('columns/(:num)/cards', 'Api\CardController::index/$1');
    $routes->post('columns/(:num)/cards', 'Api\CardController::store/$1');
    $routes->get('cards/(:num)', 'Api\CardController::show/$1');
    $routes->put('cards/(:num)', 'Api\CardController::update/$1');
    $routes->delete('cards/(:num)', 'Api\CardController::delete/$1');
});