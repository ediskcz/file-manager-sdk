<?php

namespace Edisk\FileManager\Archive;

use InvalidArgumentException;

class ArchiveFactory
{
    public const array TYPES = ['zip'];

    protected string $path;
    protected string $type;

    protected const array EXTENSION_TYPE_MAP = [
        'zip' => 'zip',
    ];

    /**
     * @throws ArchiveException
     */
    public static function load(string $path, ?string $type = null): ArchiveInterface
    {
        if (null === $type) {
            $pathInfo = pathinfo($path);
            $extension = $pathInfo['extension'];
            $type = self::EXTENSION_TYPE_MAP[$extension] ?? null;
        }
        if (!in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException('Invalid type');
        }

        return match ($type) {
            'zip' => new Zip($path),
            default => throw new InvalidArgumentException('Invalid type'),
        };
    }
}
