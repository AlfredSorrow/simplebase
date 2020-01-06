<?php

namespace SimpleBase\Functions;

function render($filepath, $variables = [], $wrapper = 'wrapper'): string
{
    $templatePath = dirname(__DIR__) . '/view/' . $filepath . '.phtml';
    $wrapperPath = dirname(__DIR__) . '/view/' . $wrapper . '.phtml';
    extract($variables);
    ob_start();
    include $wrapperPath;
    return ob_get_clean();
}

function paginate(array $articles, int $numberOfPage, int $articlesPerPage = 10): array
{
    return array_slice($articles, ($numberOfPage - 1) * $articlesPerPage, $articlesPerPage);
}
