<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');

$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::login');
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::register');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::forgotPassword');
    $routes->get('reset-password', 'AuthController::resetPassword');
    $routes->post('reset-password', 'AuthController::resetPassword');
});

$routes->group('boards', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'BoardController::index');
    $routes->post('create', 'BoardController::create');
    $routes->get('create', 'BoardController::create');
    $routes->get('(:num)', 'BoardController::show/$1');
    $routes->get('(:num)/edit', 'BoardController::edit/$1');
    $routes->post('(:num)/edit', 'BoardController::edit/$1');
    $routes->delete('(:num)', 'BoardController::delete/$1');
    $routes->post('(:num)/set-default', 'BoardController::setDefault/$1');
    $routes->post('(:num)/reorder-columns', 'BoardController::reorderColumns/$1');
});

$routes->group('columns', ['filter' => 'auth'], function ($routes) {
    $routes->post('/', 'ColumnController::create');
    $routes->put('(:num)', 'ColumnController::update/$1');
    $routes->delete('(:num)', 'ColumnController::delete/$1');
});

$routes->group('cards', ['filter' => 'auth'], function ($routes) {
    $routes->get('(:num)', 'CardController::show/$1');
    $routes->post('/', 'CardController::create');
    $routes->put('(:num)', 'CardController::update/$1');
    $routes->delete('(:num)', 'CardController::delete/$1');
    $routes->post('move', 'CardController::move');
});

$routes->group('checklists', ['filter' => 'auth'], function ($routes) {
    $routes->post('/', 'ChecklistController::create');
    $routes->post('(:num)/toggle', 'ChecklistController::toggle/$1');
    $routes->put('(:num)', 'ChecklistController::update/$1');
    $routes->delete('(:num)', 'ChecklistController::delete/$1');
    $routes->post('(:num)/reorder', 'ChecklistController::reorder/$1');
});

$routes->group('tags', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TagController::index');
    $routes->post('/', 'TagController::create');
    $routes->put('(:num)', 'TagController::update/$1');
    $routes->delete('(:num)', 'TagController::delete/$1');
    $routes->post('(:num)/add-to-card/(:num)', 'TagController::addToCard/$1/$2');
    $routes->post('(:num)/remove-from-card/(:num)', 'TagController::removeFromCard/$1/$2');
    $routes->post('(:num)/update-card-tags/(:num)', 'TagController::updateCardTags/$1/$2');
});

$routes->group('attachments', ['filter' => 'auth'], function ($routes) {
    $routes->post('(:num)/upload', 'AttachmentController::upload/$1');
    $routes->get('(:num)/download', 'AttachmentController::download/$1');
    $routes->delete('(:num)', 'AttachmentController::delete/$1');
});

$routes->group('google', ['filter' => 'auth'], function ($routes) {
    $routes->get('auth', 'GoogleController::auth');
    $routes->get('callback', 'GoogleController::callback');
    $routes->get('calendars', 'GoogleController::calendars');
    $routes->post('sync-calendar', 'GoogleController::syncCalendar');
    $routes->post('(:num)/toggle-sync', 'GoogleController::toggleSync/$1');
    $routes->post('disconnect', 'GoogleController::disconnect');
});

$routes->group('gmail', ['filter' => 'auth'], function ($routes) {
    $routes->get('senders', 'GmailController::senders');
    $routes->post('senders', 'GmailController::createSender');
    $routes->put('senders/(:num)', 'GmailController::updateSender/$1');
    $routes->delete('senders/(:num)', 'GmailController::deleteSender/$1');
    $routes->post('webhook', 'GmailController::testWebhook');
});

$routes->group('settings', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SettingsController::index');
    $routes->post('update-profile', 'SettingsController::updateProfile');
    $routes->post('update-password', 'SettingsController::updatePassword');
});