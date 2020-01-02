<?php

namespace SimpleBase\Renderer;

function render($filepath, $variables = [], $wrapper = 'wrapper')
{
    $templatePath = dirname(__DIR__) . '/view/' . $filepath . '.phtml';
    $wrapperPath = dirname(__DIR__) . '/view/' . $wrapper . '.phtml';
    extract($variables);
    ob_start();
    include $wrapperPath;
    return ob_get_clean();
}
