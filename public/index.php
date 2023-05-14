<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->redirect('/', '/contacts', 301);

$app->get('/contacts', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello contacts!");
    return $response;
});

$app->run();
