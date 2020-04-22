<?php
declare(strict_types = 1);
namespace Bitmotion\Locate\Utility;

/***
 *
 * This file is part of the "Locate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Florian Wessels <f.wessels@bitmotion.de>, Bitmotion GmbH
 *
 ***/

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocateUtility
{
    /**
     * Check the IP in the geoip table and returns iso 2 code for the current remote address
     *
     * @return bool|string
     */
    public function getCountryIso2FromIP(?string $ip = null)
    {
        $ip = $this->getNumericIp($ip);
        $tableName = self::getTableNameForIp($ip);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        return $queryBuilder
            ->select('country_code')
            ->from($tableName)
            ->where($queryBuilder->expr()->lte('ip_from', $queryBuilder->createNamedParameter($ip)))
            ->andWhere($queryBuilder->expr()->gte('ip_to', $queryBuilder->createNamedParameter($ip)))
            ->execute()
            ->fetchColumn(0);
    }

    public function getNumericIp(?string $ip = null): int
    {
        $binNum = '';

        foreach (unpack('C*', inet_pton($ip ?? GeneralUtility::getIndpEnv('REMOTE_ADDR'))) as $byte) {
            $binNum .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }

        return (int)base_convert(ltrim($binNum, '0'), 2, 10);
    }

    protected function getTableNameForIp(int $ip): string
    {
        if (strlen((string)$ip) > 10) {
            return 'static_ip2country_v6';
        }

        return 'static_ip2country_v4';
    }
}
