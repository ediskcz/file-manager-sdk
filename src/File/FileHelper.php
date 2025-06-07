<?php

namespace Edisk\FileManager\File;

use Cocur\Slugify\Slugify;
use Sovic\Common\Helpers\StringUtil;
use Transliterator;

class FileHelper
{
    public const string TYPE_AUDIO = 'audio';
    public const string TYPE_ARCHIVE = 'archive';
    public const string TYPE_DOCUMENT = 'document';
    public const string TYPE_FILE = 'file'; // generic file type
    public const string TYPE_PHOTO = 'photo';
    public const string TYPE_SUBTITLES = 'subtitles';
    public const string TYPE_VIDEO = 'video';

    public const array FILE_TYPES = [
        self::TYPE_AUDIO,
        self::TYPE_ARCHIVE,
        self::TYPE_DOCUMENT,
        self::TYPE_FILE,
        self::TYPE_PHOTO,
        self::TYPE_SUBTITLES,
        self::TYPE_VIDEO,
    ];

    public static function generateSlug(string $name, int $maxLength = 150): string
    {
        $name = StringUtil::fixUtf8($name);
        $name = trim($name, '\\/- ');
        $slugify = new Slugify(
            [
                'rulesets' => [
                    'default',
                    // Languages are preferred if they appear later, a list is ordered by number of
                    // websites in that language
                    // https://en.wikipedia.org/wiki/Languages_used_on_the_Internet#Content_languages_for_websites
                    'armenian',
                    'azerbaijani',
                    'burmese',
                    'chinese',
                    'hindi',
                    'georgian',
                    'norwegian',
                    'vietnamese',
                    'ukrainian',
                    'latvian',
                    'finnish',
                    'greek',
                    'czech',
                    'arabic',
                    'slovak',
                    'turkish',
                    'polish',
                    'german',
                    'russian',
                    'romanian',
                ],
            ]
        );
        $name = $slugify->slugify($name);
        $name = trim($name);
        $name = mb_substr($name, 0, $maxLength);

        return trim($name, '-');
    }

    public static function generateFilenameSlug(string $path, int $maxLength = 150): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return self::generateSlug($filename, $maxLength);
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

    public static function getExtension(string $file, int $maxLength = 10): string
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $ext = mb_strtolower($ext);

        return mb_substr($ext, 0, $maxLength);
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

    public static function normalizeFilename(string $filename, string $defaultName = 'new file'): string
    {
        // initial cleanup
        $trimChars = " \t\n\r\0\x0B.-_";
        $filename = trim($filename, $trimChars);
        $filename = StringUtil::fixUtf8($filename);

        $pathInfo = pathinfo($filename);
        $filename = $pathInfo['filename'];

        $filename = str_replace(['.', '-', '/', '\\', '_'], ' ', $filename);
        /** @noinspection CascadeStringReplacementInspection */
        $filename = str_replace(['@', '#', '$', '%', '^', '&'], '', $filename);

        // replace multiple spaces with a single space
        $filename = (string) preg_replace('/\s+/', ' ', $filename);
        $filename = trim($filename, $trimChars);
        // if the filename is all uppercase, convert it to a title case
        $uppercaseCount = preg_match_all('/[A-Z]/', $filename);
        if ($uppercaseCount > mb_strlen($filename) / 3) {
            $filename = mb_strtolower($filename);
            $filename = mb_convert_case($filename, MB_CASE_TITLE);
        }
        if (empty($filename)) {
            $filename = $defaultName;
        }

        // append extension
        if (isset($pathInfo['extension'])) {
            $filename .= '.' . mb_strtolower($pathInfo['extension']);
        }

        return $filename;
    }

    public static function filenameToAscii(string $filename): string
    {
        $filename = self::normalizeFilename($filename);
        // convert filename to ASCII
        // https://stackoverflow.com/questions/3542717/how-to-remove-accents-and-turn-letters-into-plain-ascii-characters/3542748#3542748
        $rules = ':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;';
        $trans = Transliterator::createFromRules($rules, Transliterator::FORWARD);
        if ($trans) {
            $transFilename = $trans->transliterate($filename);
            if ($transFilename) {
                $filename = $transFilename;
            }
        } else {
            // fallback to iconv
            $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
        }

        return $filename;
    }

    /**
     * @param string[] $needles array of strings to search for
     */
    public static function filenameContains(string $filename, array $needles, bool $all = false): bool
    {
        if (empty($needles)) {
            return false;
        }
        $filename = self::filenameToAscii($filename);
        $filename = mb_strtolower($filename);
        $result = true;
        foreach ($needles as $needle) {
            $needle = self::filenameToAscii($needle);
            $needle = mb_strtolower($needle);
            $contains = str_contains($filename, $needle);
            if ($contains && !$all) {
                return true;
            }
            $result = $result && $contains;
        }

        return $result;
    }

    public static function isEmptyDir(string $dir): bool
    {
        if (!is_readable($dir)) {
            return false;
        }

        return (count(scandir($dir, SCANDIR_SORT_NONE)) === 2);
    }
}
