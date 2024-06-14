<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'UserController::login');
$routes->get('/signup', 'UserController::signup');
$routes->post('/register', 'UserController::register');

$routes->get('/login', 'UserController::login');
$routes->get('/logout', 'UserController::logout');

$routes->get('/users', 'UserController::users');
$routes->post('/auth', 'UserController::auth');

$routes->get('/users', 'UserController::users');
$routes->get('/chat-users', 'ChatController::chat_users');
$routes->get('/chat/(:any)', 'ChatController::chats/$1');

$routes->post('/get-chat', 'ChatController::get_chat');
$routes->post('/insert-chat', 'ChatController::insert_chat');

$routes->post('/search', 'ChatController::search');





