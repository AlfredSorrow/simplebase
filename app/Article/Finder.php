<?php

namespace SimpleBase\Article;

use RecursiveDirectoryIterator;

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

        s($filteredArticle);
        return new Article($filteredArticle);
    }
    public function findAll(): array
    {
        return [];
    }
    public function findByCategory(string $category): array
    {
        return [];
    }

    private function collectArticles($path)
    {
        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);

        $articleCollection = [];

        foreach ($iterator as $file) {
            $categories = $this->getCategoriesFromPath($file->getPath());
            ['date' => $date, 'slug' => $slug] = $this->parseFileName($file->getBaseName('.md'));
            $articleCollection[] = [
                'slug' => $slug,
                'date' => $date,
                'categories' => $categories,
                'path' => $file->getRealPath()
            ];
        }

        return $articleCollection;
    }

    private function parseFileName($filename): array
    {
        $fileNameParts = explode('-', $filename);
        $date = implode('-', array_slice($fileNameParts, 0, 3));
        $slug = implode('-', array_slice($fileNameParts, 3));

        return ['date' => $date, 'slug' => $slug];
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

    private function filter($slug)
    {
    }
}
