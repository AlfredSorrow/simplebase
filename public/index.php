<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SimpleBase\Article\Finder;
use SimpleBase\User\User;
use function SimpleBase\Renderer\render as render;

require dirname(__DIR__) . '/app/Bootstrap.php';
Finder::setRootDirectory(PUBLIC_PATH);

$user = new User();

// Instantiate App
$app = \DI\Bridge\Slim\Bridge::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Main Page
$app->get('/', function (Response $response) use ($user) {
    $articles = (new Finder('blog'))->findAll();
    $response->getBody()->write(render('main', compact('articles')));
    return $response;
});

// Authorization
$app->post('/login', function (Request $request, Response $response) use ($user) {
    $pasword = $request->getParsedBody()['password'];
    $user->logIn($pasword);
    return $response;
});

$app->get('/login', function (Response $response) {
    $response->getBody()->write(render('login'));
    return $response;
});

/* Все статьи блога */
$app->get('/blog/', function (Response $response) {
    $articles = (new Finder('blog'))->findAll();
    $response->getBody()->write(render('main', compact('articles')));
    return $response;
});

$app->get('/blog/page/{number}', function ($number, Response $response) {
    $articles = (new Finder('blog'))->findAll();
    $articles = array_slice($articles, $number, 10);
    $response->getBody()->write(render('main', compact('articles', 'number')));
    return $response;
})->setName('blogPages');

$app->get('/blog/{categories:.*}/', function ($categories, Response $response) {
    $articles = (new Finder('blog'))->findByCategories(explode('/', $categories));
    $response->getBody()->write('Hello World');
    return $response;
});

$app->get('/blog/{slug}', function ($slug, Response $response) {
    $article = (new Finder('blog'))->findBySlug($slug);
    $response->getBody()->write(render('detail', compact('article')));
    return $response;
});


$app->run();
