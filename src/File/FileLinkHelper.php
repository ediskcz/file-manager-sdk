<?php

namespace Edisk\FileManager\File;

use InvalidArgumentException;
use RuntimeException;

class FileLinkHelper
{
    public static function encryptFileLink(string $url, string $secret): string
    {
        $encryptParts = [
            'url' => $url,
            'time' => time(),
        ];
        $plaintext = implode('|', $encryptParts);
        $algorithm = 'aes-256-cbc';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algorithm));
        $encryptedString = openssl_encrypt($plaintext, $algorithm, $secret, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv) . ':' . base64_encode($encryptedString);
    }

    public static function decryptFileLink(string $encrypted, string $secret): string
    {
        $parts = explode(':', $encrypted);
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Invalid encrypted string');
        }
        [$iv, $encrypted] = $parts;
        $iv = base64_decode($iv);
        $encryptedString = base64_decode($encrypted);
        $algorithm = 'aes-256-cbc';
        $decrypted = openssl_decrypt($encryptedString, $algorithm, $secret, OPENSSL_RAW_DATA, $iv);
        $decryptedParts = explode('|', $decrypted);
        if (count($decryptedParts) !== 2) {
            throw new InvalidArgumentException('Invalid encrypted string');
        }
        [$url, $time] = $decryptedParts;

        $now = time();
        $diff = $now - $time;
        $maxDiff = 60 * 60; // 1 hour
        if ($diff > $maxDiff) {
            throw new RuntimeException('File link expired');
        }

        return $url;
    }
}