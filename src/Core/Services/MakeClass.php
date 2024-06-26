<?php

namespace Shahzaib\Framework\Core\Services;

use DI\Container;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final class MakeClass
{
    public function __construct(protected Container $container)
    {

    }

    public function makeDirectory($directory, $nameSpace): void
    {

        $files = $this->scanDirectory($directory);

        foreach ($files as $file) {

            $className = $this->getClassNameFromFile($file, $directory, $nameSpace);
            $this->makeClass($className);

        }
    }

    public function getClassesFromDirectory($directory, $nameSpace): array
    {
        $files = $this->scanDirectory($directory);
        $classes = [];
        foreach ($files as $file) {
            $classes[] = $this->getClassNameFromFile($file, $directory, $nameSpace);
        }
        return $classes;
    }

    private function scanDirectory($directory): array
    {
        try {
            $iterator = new RecursiveDirectoryIterator($directory);
            $iterator = new RecursiveIteratorIterator($iterator);
            $iterator = new RegexIterator($iterator, '/\.php$/');

            $files = [];

            foreach ($iterator as $file) {

                $files[] = $file->getPathname();
            }

            return $files;
        }
        catch (\Exception) {
            return [];
        }
    }

    private function makeClass($className): void
    {
        $this->container->make($className,[]);
    }

    private function getClassNameFromFile($filePath, $dir, $nameSpace): string
    {
        $relativePath = str_replace($dir, '', $filePath);
        $relativePath = trim($relativePath, DIRECTORY_SEPARATOR);
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        $className = $nameSpace . '\\' . preg_replace('/\.php$/', '', $relativePath);

        return $className;
    }


}