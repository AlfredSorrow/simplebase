<?php

namespace SimpleBase\Article;

interface FinderInterface
{
    function __construct(string $section);
    function findBySlug(string $slug): Article;
    function findByCategories(array $categories): array;
    function findAll(): array;
    static function setRootDirectory(string $path);
}
