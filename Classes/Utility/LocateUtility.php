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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocateUtility
{
    /**
     * Check the IP in the geoip table and returns iso 2 code for the current remote address
     *
     * @param string|null $ip
     * @return bool|string
     */
    public function getCountryIso2FromIP(?string $ip = null)
    {
        $ip = $this->getNumericIp($ip);
        $tableName = $this->getTableNameForIp($ip);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        return $queryBuilder
            ->select('country_code')
            ->from($tableName)
            ->where($queryBuilder->expr()->lte('ip_from', $queryBuilder->createNamedParameter($ip)))
            ->andWhere($queryBuilder->expr()->gte('ip_to', $queryBuilder->createNamedParameter($ip)))
            ->execute()
            ->fetchOne();
    }

    public function getNumericIp(?string $ip = null): string
    {
        $ip = $ip ?? (string)GeneralUtility::getIndpEnv('REMOTE_ADDR');

        return strpos($ip, '.') !== false ? (string)ip2long($ip) : $this->convertIpv6($ip);
    }

    private function convertIpv6(string $ip): string
    {
        $ip = inet_pton($ip);
        $bin = '';
        $binNum = '';
        $decimalIp = '0';

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

    protected function getTableNameForIp(string $ip): string
    {
        return strlen($ip) > 10 ? 'static_ip2country_v6' : 'static_ip2country_v4';
    }
}
