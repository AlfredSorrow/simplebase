<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SimpleBase\Article\Article;
use SimpleBase\Article\Finder;
use function SimpleBase\Renderer\render as render;

require dirname(__DIR__) . '/app/Bootstrap.php';
echo ini_get('display_errors');
Finder::setRootDirectory(PUBLIC_PATH);

// Instantiate App
$app = \DI\Bridge\Slim\Bridge::create();

new Parsedown;
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add routes
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(render('main'));
    return $response;
});

/* Все статьи блога */
$app->get('/blog/', function ($slug, Response $response) {
    $response->getBody()->write(render('main', ['slug' => $slug]));
    return $response;
});

$app->get('/blog/page/{number}', function ($slug, Response $response) {
    $response->getBody()->write(render('main', ['slug' => $slug]));
    return $response;
});

$app->get('/blog/{section}/', function ($section, Response $response) {
    $response->getBody()->write(render('main', ['section' => $section]));
    return $response;
});

$app->get('/blog/{slug}', function ($slug, Response $response) {
    $aricle = (new Finder('blog'))->findBySlug($slug);
    $response->getBody()->write(render('main', ['slug' => $slug]));
    return $response;
});


$app->run();
