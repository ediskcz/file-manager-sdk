<?php

namespace Edisk\FileManager\File;

class FileSizeHelper
{
    public static function humanReadableFileSize(
        int     $size,
        ?string $unit = null,
        int     $decimals = 0,
        ?string $decimalSeparator = '.',
        ?string $thousandsSeparator = ','
    ): string {
        if ((!$unit && $size >= 1 << 30) || $unit === 'GB') {
            return number_format($size / (1 << 30), $decimals, $decimalSeparator, $thousandsSeparator) . ' GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit === 'MB') {
            return number_format($size / (1 << 20), $decimals, $decimalSeparator, $thousandsSeparator) . ' MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit === 'KB') {
            return number_format($size / (1 << 10), $decimals, $decimalSeparator, $thousandsSeparator) . ' KB';
        }

        return number_format($size) . " b";
    }

    public static function urlSlug(int $size, ?string $unit = null): string
    {
        $filesizeString = self::humanReadableFileSize($size, $unit, 2, '.', '');
        $filesizeString = str_replace([' '], ['-'], $filesizeString);

        return mb_strtolower($filesizeString);
    }
}
