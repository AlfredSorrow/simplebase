<?php

namespace SimpleBase\Article;

use Parsedown;

class Article
{
    private $text;
    private $category;

    public function __construct($text, $category = '')
    {
        $this->text = $text;
        $this->category = $category;
        $this->parse(new Parsedown());
    }

    public function render()
    {
    }

    private function parse($parsedown)
    {
        return $parsedown->text('');
    }
}
