<?php

namespace Edisk\FileManager\Video;

class VideoMeta
{
    public static function isHd(string $resolution): bool
    {
        if (self::is4k($resolution)) {
            return false;
        }

        $resolutionData = explode('*', $resolution);

        return count($resolutionData) === 2 && $resolutionData[0] >= 1280 && $resolutionData[1] >= 720;
    }

    public static function is4k(string $resolution): bool
    {
        $resolutionData = explode('*', $resolution);

        return count($resolutionData) === 2 && $resolutionData[0] >= 3840 && $resolutionData[1] >= 1480;
    }

    /**
     * @return array
     * [
     *  'duration' => (int) seconds,
     *  'hours' => (int) hours
     *  'minutes' => (int) minutes
     *  'seconds' => (int) seconds
     *  'number_format' => (string) '1:48:11',
     *  'text_format' => (string) '1h 48m 11s',
     * ]
     */
    public static function parseDuration(float $seconds): array
    {
        $dur = floor($seconds);
        $h = floor($dur / (60 * 60));
        $m = floor(($dur - ($h * 3600)) / 60);
        $s = floor($dur - $h * 3600 - $m * 60);

        return [
            'duration' => $seconds,
            'hours' => $h,
            'minutes' => $m,
            'seconds' => $s,
            'number_format' => $h . ':' . $m . ':' . $s,
            'text_format' => $h . 'h' . ' ' . $m . 'm ' . $s . 's',
        ];
    }
}
