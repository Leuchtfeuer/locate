<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
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
     * @param string|null $ip
     * @return bool|string
     * @throws Exception
     */
    public function getCountryIso2FromIP(?string $ip = null): bool|string
    {
        $ip = $this->getNumericIp($ip);
        if ($ip === false) {
            return false;
        }
        $tableName = $this->getTableNameForIp($ip);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        // Only the last range starting at or below the IP can contain it. Reading it backwards along idx_ip_from
        // touches a single row, while a condition on both ip_from and ip_to results in a (full) range scan.
        $range = $queryBuilder
            ->select('country_code', 'ip_to')
            ->from($tableName)
            ->where($queryBuilder->expr()->lte('ip_from', $queryBuilder->createNamedParameter($ip)))
            ->orderBy('ip_from', 'DESC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($range === false || $this->compareNumericStrings((string)$range['ip_to'], $ip) < 0) {
            return false;
        }

        return $range['country_code'];
    }

    public function getNumericIp(?string $ip = null): string|bool
    {
        $ip = $ip ?? $this->getRemoteAddress();

        // IPv4-mapped IPv6 addresses (::ffff:192.0.2.1, e.g. from dual-stack sockets) are looked up as IPv4
        if (str_starts_with(strtolower($ip), '::ffff:') && filter_var(substr($ip, 7), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            $ip = substr($ip, 7);
        }

        if (str_contains($ip, '.')) {
            $long = ip2long($ip);

            return $long === false ? false : (string)$long;
        }

        return $this->convertIpv6($ip);
    }

    /**
     * The client address as determined by TYPO3. Behind a reverse proxy, TYPO3 takes it from X-Forwarded-For once
     * $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'] and ['reverseProxyHeaderMultiValue'] are configured.
     * Reading the header directly would let every visitor choose the country to be located in.
     */
    protected function getRemoteAddress(): ?string
    {
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
                foreach (unpack('C*', $ip) as $byte) {
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

    /**
     * Compares two non-negative integers given as decimal strings. IPv6 numbers exceed PHP_INT_MAX.
     */
    private function compareNumericStrings(string $a, string $b): int
    {
        $a = ltrim($a, '0');
        $b = ltrim($b, '0');

        return strlen($a) <=> strlen($b) ?: strcmp($a, $b) <=> 0;
    }

    protected function getTableNameForIp(string $ip): string
    {
        return strlen($ip) > 10 ? 'static_ip2country_v6' : 'static_ip2country_v4';
    }
}
