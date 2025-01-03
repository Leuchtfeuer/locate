<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Utility;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocateUtility
{
    /**
     * Check the IP in the geoip table and returns iso 2 code for the current remote address
     *
     * @throws Exception
     */
    public function getCountryIso2FromIP(?string $ip = null): bool|string
    {
        $ip = $this->getNumericIp($ip);
        if (!is_string($ip)) {
            return false;
        }
        $tableName = $this->getTableNameForIp($ip);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        $countryCode = $queryBuilder
            ->select('country_code')
            ->from($tableName)
            ->where($queryBuilder->expr()->lte('ip_from', $queryBuilder->createNamedParameter($ip)))->andWhere($queryBuilder->expr()->gte('ip_to', $queryBuilder->createNamedParameter($ip)))->executeQuery()
            ->fetchOne();

        if ($countryCode !== false) {
            return TypeCaster::toString($countryCode);
        }

        return false;
    }

    public function getNumericIp(?string $ip = null): string|bool
    {
        $ip ??= $this->getRemoteAddress();
        if ($ip === null) {
            return false;
        }

        return str_contains($ip, '.') ? (string)ip2long($ip) : $this->convertIpv6($ip);
    }

    protected function getRemoteAddress(): ?string
    {
        $remoteAddr = $this->getHeader('X-Forwarded-For');
        if ($remoteAddr !== null && $remoteAddr !== '' && $remoteAddr !== '0') {
            return $remoteAddr;
        }
        return (string)GeneralUtility::getIndpEnv('REMOTE_ADDR');
    }

    protected function getHeader(string $headerName): ?string
    {
        $headers = getallheaders();
        return $headers[$headerName] ?? null;
    }

    private function convertIpv6(string $ip): string|bool
    {
        $ip = inet_pton($ip);
        $bin = '';
        $binNum = '';
        $decimalIp = '0';

        if (is_bool($ip)) {
            return false;
        }

        for ($bit = strlen($ip) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip[$bit])) . $bin;
        }

        switch (true) {
            case function_exists('gmp_init'):
                $decimalIp = gmp_strval(gmp_init($bin, 2), 10);
                break;

            case function_exists('bcadd'):
                $max = strlen($bin);
                for ($i = 0; $i < $max; $i++) {
                    $decimalIp = bcmul($decimalIp, '2');
                    $decimalIp = bcadd($decimalIp, $bin[$i]);
                }
                break;

            default:
                $data = unpack('C*', $ip);
                if ($data === false) {
                    return false;
                }
                foreach ($data as $byte) {
                    $binNum .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
                }

                $decimalIp = base_convert(ltrim($binNum, '0'), 2, 10);
        }

        return $decimalIp;
    }

    public static function mainstreamValue(string &$value): void
    {
        $value = mb_strtolower(str_replace('-', '_', $value));
    }

    protected function getTableNameForIp(string $ip): string
    {
        return strlen($ip) > 10 ? 'static_ip2country_v6' : 'static_ip2country_v4';
    }
}
