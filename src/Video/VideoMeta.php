<?php

namespace FileManager\Video;

class VideoMeta
{
    public static function isHd(string $resolution): bool
    {
        if (self::is4k($resolution)) {
            return false;
        }

        $resolution_array = explode('*', $resolution);

        return count($resolution_array) === 2 && $resolution_array[0] >= 1280 && $resolution_array[1] >= 720;
    }

    public static function is4k(string $resolution): bool
    {
        $resolution_array = explode('*', $resolution);

        return count($resolution_array) === 2 && $resolution_array[0] >= 3840 && $resolution_array[1] >= 1480;
    }
}
