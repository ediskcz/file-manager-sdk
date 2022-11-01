<?php

namespace App\File;

use Cocur\Slugify\Slugify;

class FileHelper
{
    public const TYPE_AUDIO = 'audio';
    public const TYPE_ARCHIVE = 'archive';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_FILE = 'file';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_SUBTITLES = 'subtitles';
    public const TYPE_VIDEO = 'video';

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

    public static function getFileTypeFromExtension(string $extension): string
    {
        switch ($extension) {

            case 'rar':
            case 'zip':
            case'4z':
            case'tar':
            case'gz':
            case'iso':
                return self::TYPE_ARCHIVE;
            case'mpg':
            case'mpeg':
            case'wmv':
            case'3gp':
            case'mov':
            case'asf':
            case'wma':
            case'avi':
            case'flv':
            case'mp4':
                return self::TYPE_VIDEO;
            case'mp3':
            case'flac':
            case'wav':
            case'ogg':
            case'acc':
                return self::TYPE_AUDIO;
            case'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'tiff':
            case 'raw':
            case 'nef':
                return self::TYPE_PHOTO;
            case 'sub':
            case 'srt':
                return self::TYPE_SUBTITLES;
            case 'php':
            case 'html':
            case 'htm':
            case 'xml':
            case 'text':
            case 'txt':
            case 'js':
            case 'vbs':
            case 'dtd':
            case 'asc':
            case 'cs':
            case 'css':
            case 'c':
            case 'cpp':
            case 'conf':
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
                return self::TYPE_DOCUMENT;
            default:
                return self::TYPE_FILE;
        }
    }
}
