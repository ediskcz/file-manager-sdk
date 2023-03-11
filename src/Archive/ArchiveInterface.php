<?php

namespace FileManager\Archive;

interface ArchiveInterface
{
    /**
     * Try to open an archive and retrieve content
     *
     * @return array array of files
     * [
     *  'name' => '/path/to/file/filename.ext',
     *  'filesize' => 'filesize in bytes',
     *  'filesize_readable' => 'human-readable filesize',
     *  'extension' => 'extension'
     * ]
     */
    public function getListFiles(): array;

    public function getFilesCount(): int;

    public function isPasswordProtected(): bool;
}
