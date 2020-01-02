<?php

namespace SimpleBase\Article;

use Exception;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use RecursiveIteratorIterator;

class Finder implements FinderInterface
{
    private $section;
    private $articles;

    static $rootDirectory;

    public static function setRootDirectory($path)
    {
        self::$rootDirectory = $path;
    }

    public function __construct($section)
    {
        $this->section = $section;
        $this->sectionPath = self::$rootDirectory . DIRECTORY_SEPARATOR . $this->section;
    }

    public function findBySlug($slug): Article
    {
        $articleCollection = $this->collectArticles($this->sectionPath);
        $filteredArticle = array_filter($articleCollection, function ($article) use ($slug) {
            return $article['slug'] === $slug;
        });

        return new Article(array_pop($filteredArticle));
    }
    public function findAll(): array
    {
        $articleCollection = $this->collectArticles($this->sectionPath);

        return array_map(function ($article) {
            return new Article($article);
        }, $articleCollection);
    }
    public function findByCategories(array $categories): array
    {
        $categoryPath = $this->sectionPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $categories);
        $articleCollection = $this->collectArticles($categoryPath);

        return array_map(function ($article) {
            return new Article($article);
        }, $articleCollection);
    }

    private function collectArticles(string $path): array
    {
        if (!is_dir($path)) {
            throw new Exception('This Category does not exist');
        }
        $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory);

        $articleCollection = [];

        foreach ($iterator as $file) {
            $categories = $this->getCategoriesFromPath($file->getPath());
            ['date' => $date, 'slug' => $slug] = $this->parseFileName($file->getBaseName('.md'));
            $articleCollection[] = [
                'slug'          => $slug,
                'date'          => $date,
                'categories'    => $categories,
                'path'          => $file->getRealPath()
            ];
        }

        return $articleCollection;
    }

    private function parseFileName($filename): array
    {
        $fileNameParts = explode('-', $filename);
        $date = implode('-', array_slice($fileNameParts, 0, 3));
        $slug = implode('-', array_slice($fileNameParts, 3));

        return compact('date', 'slug');
    }

    private function getCategoriesFromPath($path)
    {
        return array_filter(
            explode(
                DIRECTORY_SEPARATOR,
                str_replace($this->sectionPath, '', $path)
            )
        );
    }

    private function sort($order = 'desc'): array
    {
        return [];
    }
}
