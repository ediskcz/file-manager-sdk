<?php

namespace FileManager\Utils;

class StringHelper
{
    public static function fixUtf8(string $value): string
    {
        return iconv('UTF-8', 'UTF-8//IGNORE', $value);
    }
}
