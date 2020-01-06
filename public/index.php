<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SimpleBase\Article\Finder;
use SimpleBase\User\User;
use function SimpleBase\Functions\render;
use function SimpleBase\Functions\paginate;

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

$sections = [
    [
        'name' => 'blog',
        'isSharable' => true,
        'isPublic' => true,
    ],
    [
        'name' => 'shared',
        'isSharable' => true,
        'isPublic' => false,
    ],
    [
        'name' => 'private',
        'isSharable' => false,
        'isPublic' => false,
    ],
];


/* Все статьи секций */
foreach ($sections as $section) {
    $app->get("/{$section['name']}/", function (Response $response) use ($section, $user) {
        if ($section['isPublic'] || $user->isAuthorized()) {
            $articles = (new Finder($section['name']))->findAll();
            $response->getBody()->write(render('main', compact('articles')));
            return $response;
        }

        $response->getBody()->write(render('restricted'));
        return $response->withStatus(401);
    });

    $app->get("/{$section['name']}/page/{number}", function ($number, Response $response) use ($section, $user) {
        if ($section['isPublic'] || $user->isAuthorized()) {
            $articles = (new Finder($section['name']))->findAll();
            $articles = paginate($articles, $number, 5);
            $response->getBody()->write(render('main', compact('articles', 'number')));
            return $response;
        }
        $response->getBody()->write(render('restricted'));
        return $response->withStatus(401);
    })->setName("{$section['name']}Pages");

    $app->get("/{$section['name']}/{categories:.*}/", function ($categories, Response $response) use ($section, $user) {
        if ($section['isPublic'] || $user->isAuthorized()) {
            $articles = (new Finder($section['name']))->findByCategories(explode('/', $categories));
            $response->getBody()->write(render('main', compact('articles')));
            return $response;
        }
        $response->getBody()->write(render('restricted'));
        return $response->withStatus(401);
    });

    $app->get("/{$section['name']}/{slug}", function ($slug, Response $response) use ($section, $user) {
        if ($section['isSharable'] || $user->isAuthorized()) {
            $article = (new Finder($section['name']))->findBySlug($slug);
            $response->getBody()->write(render('detail', compact('article')));
            return $response;
        }
        $response->getBody()->write(render('restricted'));
        return $response->withStatus(401);
    });
}


$app->run();
