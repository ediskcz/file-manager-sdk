<?php

namespace Edisk\FileManager\Tests\File;

use Edisk\FileManager\File\FileHelper;
use PHPUnit\Framework\TestCase;

/**
 * @group files
 */
class FileHelperTest extends TestCase
{
    public function urlProvider(): array
    {
        $data = [];
        // Test 1: Basic test with alphanumeric characters and underscores
        $data[] = ['example_file1.txt', 'example-file1'];
        // Test 2: Test with spaces in the filename
        $data[] = ['example file 2.jpg', 'example-file-2'];
        // Test 3: Test with uppercase characters
        $data[] = ['Example_File_3.PNG', 'example-file-3'];
        // Test 4: Test with special characters in the filename
        // $data[] = ['@#$%^&.txt', ''];
        // Test 5: Test with leading and trailing spaces
        $data[] = ['  leading_trailing_spaces.txt  ', 'leading-trailing-spaces'];
        // Test 6: Test with consecutive spaces
        $data[] = ['file   with   consecutive   spaces.txt', 'file-with-consecutive-spaces'];
        // Test 7: Test with an empty filename
        $data[] = ['', ''];
        // Test 8: Test with a long filename - length
        $data[] = [
            ' this_is_a_very_long_filename_with_a_lot_of_characters_and_numbers_1234567890.txt',
            'this-is-a-very-long-filename-with-a-lot-of-characters-and-numbers',
        ];
        // Test 9: Test with non-Latin characters
        $data[] = ['中文文件.jpg', 'zhongwenwenjian'];
        $data[] = ['vietnamské rýžové rolky gỏi cuốn', 'vietnamske-ryzove-rolky-goi-cuon'];
        // Test 10: Test with a filename containing only numbers
        $data[] = ['1234567890.txt', '1234567890'];

        return $data;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGenerateFilenameSlug(string $input, string $expected): void
    {
        $result = FileHelper::generateFilenameSlug($input, 65);

        self::assertEquals($expected, $result);
    }
}
