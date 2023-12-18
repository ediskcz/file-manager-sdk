<?php /** @noinspection SpellCheckingInspection */

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

    public function filenameProvider(): array
    {
        $data = [];
        // Test 1: Basic test with alphanumeric characters and underscores
        $data[] = ['example_file1.txt', 'example file1.txt'];
        // Test 2: Test with spaces in the filename
        $data[] = ['example file 2.jpg', 'example file 2.jpg'];
        // Test 3: Test with uppercase characters
        $data[] = ['Example_File_3.PNG', 'Example File 3.png'];
        // Test 4: Test with special characters in the filename, test empty filename
        $data[] = ['@#$%^&.txt', 'new file.txt'];
        // Test 5: Test with leading and trailing spaces
        $data[] = ['  leading_trailing_spaces.txt  ', 'leading trailing spaces.txt'];
        // Test 6: Test with consecutive spaces
        $data[] = ['file   with   consecutive   spaces.txt', 'file with consecutive spaces.txt'];
        // Test 7: Test with an empty filename
        $data[] = ['', 'new file'];
        // Test 8: Test with a long filename - length
        $data[] = [
            ' this_is_a_very_long_filename_with_a_lot_of_characters_and_numbers_1234567890.txt',
            'this is a very long filename with a lot of characters and numbers 1234567890.txt',
        ];
        // Test 9: Test with non-Latin characters
        $data[] = ['中文文件.jpg', '中文文件.jpg'];
        $data[] = ['vietnamské rýžové rolky gỏi cuốn', 'vietnamské rýžové rolky gỏi cuốn'];
        // Test 10: Test with a filename containing only numbers
        $data[] = ['1234567890.txt', '1234567890.txt'];
        // Test 11: Many uppercase characters
        $data[] = [
            'THIS_IS_A_VERY_LONG_FILENAME_WITH_A_LOT_OF_CHARACTERS_AND_NUMBERS_1234567890.txt',
            'This Is A Very Long Filename With A Lot Of Characters And Numbers 1234567890.txt',
        ];

        return $data;
    }

    /**
     * @dataProvider filenameProvider
     */
    public function testNormalizeFilename(string $input, string $expected): void
    {
        $result = FileHelper::normalizeFilename($input);

        self::assertEquals($expected, $result);
    }

    public function filenameContainsProvider(): array
    {
        $data = [];
        $data[] = ['Ulice', ['ulice'], true];
        $data[] = ['Zlatá Labuť', ['zlata', 'labut'], true];
        $data[] = ['eliška-a--damian', ['eliška a damián'], true];
        $data[] = ['Matematika--_ zločinu', ['matematika zlocinu'], true];
        $data[] = ['Hello___World   ', ['Hello World'], true];
        // Non-ASCII characters with accents
        $data[] = ['Café français', ['cafe'], true];
        // Non-ASCII characters with tilde
        $data[] = ['Español', ['espanol'], true];
        $data[] = ['Łeba', ['Leba'], true];
        // Non-ASCII characters from Cyrillic alphabet
        $data[] = ['Русский язык', ['russkij'], true];
        // Special characters
        $data[] = ['123 Special Characters: @#$%^&*()-=_+[]{}|;:\'",.<>?`', ['@'], false];
        // Emoji characters
        $data[] = ['Some Emoji 😀🌍❤️', ['😀'], true];

        return $data;
    }

    /**
     * @dataProvider filenameContainsProvider
     */
    public function testFilenameContains(string $input, array $contains, bool $expected): void
    {
        $result = FileHelper::filenameContains($input, $contains);

        self::assertEquals($expected, $result);
    }
}
