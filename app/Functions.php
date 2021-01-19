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

function prepareCategories(array $categories, array $attributesToTag): string
{
    if (empty($categories)) {
        return '';
    }
    $ulAttr = !empty($attributesToTag['ul']) ? " {$attributesToTag['ul']} " : '';
    $aAttr = !empty($attributesToTag['a']) ? " {$attributesToTag['a']} " : '';
    $liAttr = !empty($attributesToTag['li']) ? " {$attributesToTag['li']} " : '';
    $html = "<ul {$ulAttr} >";
    foreach ($categories as $category) {
        $html .= "<li{$liAttr}> <a href='{$category['link']}'{$aAttr}>{$category['name']}</a>";
        if (!empty($category['childs'])) {
            $html .= prepareCategories($category['childs'], $attributesToTag);
        }
        $html .= '</li>';
    }

    return "{$html}</ul>";
}

$array = range(1, 11);

function permutation($origin): array
{
    $factorial = function ($n) {
        $factorial = 1;
        for ($i = 1; $i <= $n; $i++) {
            $factorial *= $i;
        }

        return $factorial;
    };

    $permutations = [];
    $permutations[] = $origin;
    
    $size = count($origin);
    $n = $factorial($size);
    $a = 0;
    for ($i = 1; $i < $n; $i++) {
        $tmp = $origin[$a];
        $origin[$a] = $origin[$a + 1];
        $origin[$a + 1] = $tmp;
        $permutations[] = $origin;
        $a++;

        if ($a + 1 === $size) {
            $a = 0;
        }
    }

    return $permutations;
}
