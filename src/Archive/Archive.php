<?php

namespace Edisk\FileManager\Archive;

use InvalidArgumentException;

abstract class Archive implements ArchiveInterface
{
    private string $path;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException('Not a file');
        }

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
