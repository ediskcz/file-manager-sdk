<?php

namespace Edisk\FileManager\Archive;

use Edisk\FileManager\File\FileSizeHelper;
use PhpZip\Exception\ZipEntryNotFoundException;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;

final class Zip extends Archive
{
    private ZipFile $zipFile;

    /**
     * @throws ArchiveException
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
        try {
            $zipFile = new ZipFile();
            $zipFile->openFile($path);
        } catch (ZipException $e) {
            throw new ArchiveException('Unable to open zip file', 1, $e);
        }

        $this->zipFile = $zipFile;
    }

    public function isPasswordProtected(): bool
    {
        return false;
    }


    /**
     * @throws ArchiveException
     */
    public function getListFiles(): array
    {
        $files = $this->zipFile->getListFiles();
        $result = [];
        foreach ($files as $file) {
            try {
                $entry = $this->zipFile->getEntry($file);
            } catch (ZipEntryNotFoundException $e) {
                throw new ArchiveException('Unable to read zip entry', 2, $e);
            }
            if ($entry->isDirectory() || $entry->isUnixSymlink()) {
                continue;
            }

            $name = $entry->getName();
            $pathInfo = pathinfo($name);
            $filesize = $entry->getUncompressedSize();
            $filesizeReadable = FileSizeHelper::humanReadableFileSize($filesize);
            $result[] = [
                'type' => 'file',
                'filename' => basename($name),
                'filesize' => $filesize,
                'filesize_readable' => $filesizeReadable,
                'extension' => $pathInfo['extension'] ?? null,
            ];
        }

        return $result;
    }

    public function getFilesCount(): int
    {
        return $this->zipFile->count();
    }
}
