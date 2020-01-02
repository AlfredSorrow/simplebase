<?php

namespace SimpleBase\Article;

use DateTime;
use Parsedown;

class Article
{
    private $slug;
    private $date;
    private $categories;
    private $path;
    private $parser;

    public function __construct(array $articleMeta)
    {
        $this->slug =       $articleMeta['slug'];
        $this->date =       $articleMeta['date'];
        $this->categories = $articleMeta['categories'];
        $this->path =       $articleMeta['path'];
        $this->parser =     new Parsedown();
    }

    public function text(): string
    {
        return $this->parser->text(file_get_contents($this->path));
    }

    public function getDate(string $format = 'd-m-Y'): string
    {
        return (new DateTime($this->date))->format($format);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
