<?php

namespace SimpleBase\Article;

interface FinderInterface
{
    function __construct(string $section);
    function findBySlug(string $slug): Article;
    function findByCategory(string $category): array;
    function findAll(): array;
}
