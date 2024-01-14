<?php

namespace Edisk\FileManager\Tests\File;

use Edisk\FileManager\File\FileSizeHelper;
use PHPUnit\Framework\TestCase;

/**
 * @group files
 */
class FileSizeHelperTest extends TestCase
{
    public function urlSlugProvider(): array
    {
        $data = [];
        $data[] = [1070330054, '1020.75-mb'];
        $data[] = [1090330054, '1.02-gb'];
        $data[] = [10703300054, '9.97-gb'];

        return $data;
    }

    /**
     * @dataProvider urlSlugProvider
     */
    public function testUrlSlug(int $input, string $expected): void
    {
        $actual = FileSizeHelper::urlSlug($input);
        self::assertEquals($expected, $actual);
    }
}
