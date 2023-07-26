<?php

namespace Edisk\FileManager\Tests\File;

use Edisk\FileManager\File\FileLinkHelper;
use PHPUnit\Framework\TestCase;

/**
 * @group files
 */
class FileLinkTest extends TestCase
{
    public function testFileLinkHelperEncryptDecrypt(): void
    {
        $url = $expected = '90380/tv-show-part-124-some-name-lang-cz.html';
        $secret = 'some-secret';
        $encrypted = FileLinkHelper::encryptFileLink($url, $secret);
        $decrypted = FileLinkHelper::decryptFileLink($encrypted, $secret);

        self::assertEquals($expected, $decrypted);
    }
}
