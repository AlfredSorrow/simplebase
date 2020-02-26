<?php

namespace SimpleBase\Finder;

use SimpleBase\Article\Article;

use Exception;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use RecursiveIteratorIterator;
use DateTime;

class Finder implements FinderInterface
{
    private $section = '';

    private $rootDirectory = '';

    public function __construct(string $path)
    {
        $this->rootDirectory = $path;
        $this->sectionPath = $path;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;
        $this->sectionPath = $this->rootDirectory . DIRECTORY_SEPARATOR . $this->section;
        return $this;
    }

    public function getArticleBySlug(string $slug): Article
    {
        $articleCollection = $this->collectArticles($this->sectionPath);
        $filteredArticle = array_filter($articleCollection, function ($article) use ($slug) {
            return $article['slug'] === $slug;
        });

        if (count($filteredArticle) > 1) {
            throw new Exception('Two articles with same slug');
        }

        if (empty($filteredArticle)) {
            throw new Exception('Not Found');
        }

        return new Article(current($filteredArticle));
    }
    public function getAllArticles(): array
    {
        $articleCollection = $this->collectArticles($this->sectionPath);

        $articles = array_map(function ($article) {
            return new Article($article);
        }, $articleCollection);

        return $this->sort($articles);
    }
    public function getArticlesByCategories(array $categories): array
    {
        $categoryPath = $this->sectionPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $categories);
        $articleCollection = $this->collectArticles($categoryPath);

        $articles = array_map(function ($article) {
            return new Article($article);
        }, $articleCollection);

        return $this->sort($articles);
    }

    private function collectArticles(string $path): array
    {
        if (!is_dir($path)) {
            throw new Exception('This category does not exist');
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
                'path'          => $file->getRealPath(),
                'section'       => $this->section
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

    private function sort(array $articles, $order = 'desc'): array
    {
        usort($articles, function ($prevArticle, $nextArticle) use ($order) {
            $prevDate = new DateTime($prevArticle->getDate());
            $nextDate = new DateTime($nextArticle->getDate());
            if ($order === 'desc') {
                return  $nextDate <=> $prevDate;
            }

            return  $prevDate <=> $nextDate;
        });
        return $articles;
    }

    public function getCategories(string $dir = $this->sectionPath): array
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
                'childs' => self::getCategories($file->getPathname())
            ];
        }
    
        return $dirs;
    }
}
