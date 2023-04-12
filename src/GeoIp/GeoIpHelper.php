<?php

namespace Edisk\FileManager\GeoIp;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use RuntimeException;

class GeoIpHelper
{
    /**
     * @throws InvalidDatabaseException
     */
    private static function getGeoIpReader(): Reader
    {
        $geoIpDatabasePath = __DIR__ . '/Database/GeoLite2-Country.mmdb';
        if (!file_exists($geoIpDatabasePath)) {
            throw new RuntimeException('Unable to read GeoIp database');
        }

        return new Reader($geoIpDatabasePath);
    }

    /**
     * @param string $ip
     * @return string|null The two-character ISO 3166-1 alpha code
     */
    public static function detectIpCountry(string $ip): ?string
    {
        try {
            $reader = self::getGeoIpReader();
            $record = $reader->country($ip);
        } catch (AddressNotFoundException) {
            // not found
            return null;
        } catch (Exception $e) {
            throw new RuntimeException('Unable to detect ip with GeoIp database', 0, $e);
        }

        return $record->country->isoCode;
    }
}
