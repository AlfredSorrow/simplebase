<?php

namespace SimpleBase\Finder;
use SimpleBase\Article\Article;

interface FinderInterface
{
    function __construct(string $path);
    function setSection(string $section): Finder;
    function getArticleBySlug(string $slug): Article;
    function getArticlesByCategories(array $categories): array;
    function getAllArticles(): array;
    //function getCategories(): array;
}
