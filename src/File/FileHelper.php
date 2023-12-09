<?php

namespace Edisk\FileManager\File;

use Cocur\Slugify\Slugify;

class FileHelper
{
    public const TYPE_AUDIO = 'audio';
    public const TYPE_ARCHIVE = 'archive';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_FILE = 'file'; // generic file type
    public const TYPE_PHOTO = 'photo';
    public const TYPE_SUBTITLES = 'subtitles';
    public const TYPE_VIDEO = 'video';

    public const FILE_TYPES = [
        self::TYPE_AUDIO,
        self::TYPE_ARCHIVE,
        self::TYPE_DOCUMENT,
        self::TYPE_FILE,
        self::TYPE_PHOTO,
        self::TYPE_SUBTITLES,
        self::TYPE_VIDEO,
    ];

    public const MAX_FILE_URL_LENGTH = 150;

    public static function generateUrl(string $name): string
    {
        $name = trim($name, '\\/- ');
        $slugify = new Slugify();
        $name = $slugify->slugify($name);
        $name = trim($name);

        return mb_substr($name, 0, self::MAX_FILE_URL_LENGTH);
    }

    public static function deleteDirectory(string $dir, bool $keepDir = false): bool
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir, SCANDIR_SORT_NONE) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            self::deleteDirectory($dir . '/' . $item);
        }
        if (!$keepDir) {
            return rmdir($dir);
        }

        return true;
    }

    public static function getMimeType(string $file): string
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $file);
        finfo_close($fileInfo);

        return $mime;
    }

    public static function getExtension(string $file): string
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        return mb_strtolower($ext);
    }

    public static function getFileTypeFromExtension(string $extension): string
    {
        return match ($extension) {
            'rar', 'zip', '4z', 'tar', 'gz', 'iso' => self::TYPE_ARCHIVE,
            'mpg', 'mpeg', 'wmv', '3gp', 'mov', 'asf', 'wma', 'avi', 'flv', 'mp4' => self::TYPE_VIDEO,
            'mp3', 'flac', 'wav', 'ogg', 'acc' => self::TYPE_AUDIO,
            'jpg', 'jpeg', 'png', 'gif', 'tiff', 'raw', 'nef' => self::TYPE_PHOTO,
            'sub', 'srt' => self::TYPE_SUBTITLES,
            'php', 'html', 'htm', 'xml', 'text', 'txt', 'js', 'vbs', 'dtd', 'asc', 'cs', 'css', 'c', 'cpp', 'conf', 'pdf', 'doc', 'docx', 'xls' => self::TYPE_DOCUMENT,
            default => self::TYPE_FILE,
        };
    }
}
