<?php

namespace SimpleBase\Functions;

use SimpleBase\Article\Article;

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

/* function getCategories(array $articles): array
{
    $categories = [];
    $id = 0;
    foreach ($articles as $article) {
        foreach ($article->getCategories() as $level => $category) {
            $categoryStruct = [
                'name' => $category,
                'parent' => $id > 0 ? $id - 1 : $id,
            ];
            $categories[$id] = $categoryStruct;
            $id++;
        }
    }
    s(array_unique($categories, SORT_REGULAR));
    return $categories;
} */

function getCategories($dir): array
{
    if (empty($dir)) {
        return [];
    }

    $dirs = [];
    $directoryIterator = new \DirectoryIterator($dir);
    foreach ($directoryIterator as $file) {
        if (
            $file->isDot()
            || !$file->isDir()
            || !$file->isReadable()
        ) {
            continue;
        }

        $dirs[] = [
            'name' => $file->getBasename(),
            'path' => $file->getPathname(),
            'childs' => getCategories($file->getPathname())
        ];
    }

    return $dirs;
}

