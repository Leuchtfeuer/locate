<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Tools;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IP2Country
 */
abstract class IP2Country
{
    /**
     * Check the IP in the geoip table and returns iso 2 code for the current remote address
     *
     * @return bool|string
     */
    public static function getCountryIso2FromIP(int $ip)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_locate_ip2country');

        return $queryBuilder
            ->select('iso2')
            ->from('static_ip2country')
            ->where($queryBuilder->expr()->lte('ipfrom', $queryBuilder->createNamedParameter($ip)))
            ->andWhere($queryBuilder->expr()->gte('ipto', $queryBuilder->createNamedParameter($ip)))
            ->execute()
            ->fetchColumn(0);
    }
}
