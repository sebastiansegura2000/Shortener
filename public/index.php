<?php

require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/controllers/LinkController.php';

$router = new Router();
$linkController = new LinkController();

$router->post('/shorten', [$linkController, 'shorten']);
$router->get('/links', [$linkController, 'list']);
$router->delete('/delete', [$linkController, 'delete']);
$router->get('/health', [$linkController, 'health']);
$router->get('/{shortCode}', [$linkController, 'redirect']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);