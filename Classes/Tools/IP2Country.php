<?php

namespace Bitmotion\Locate\Tools;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class IP2Country
 *
 * @package Bitmotion\Locate\Tools
 */
abstract class IP2Country
{

    /**
     * Check the IP in the geoip table and returns iso 2 code for the current remote address
     *
     * @param integer $IP as long remote address
     * @return    false|string Example: DE
     */
    public static function GetCountryIso2FromIP($IP)
    {
        if (class_exists(\TYPO3\CMS\Core\Database\ConnectionPool::class)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_locate_ip2country');
            $statement = $queryBuilder
                ->select('iso2')
                ->from('tx_locate_ip2country')
                ->where($queryBuilder->expr()->lte('ipfrom', $queryBuilder->createNamedParameter($IP)))
                ->andWhere($queryBuilder->expr()->gte('ipto', $queryBuilder->createNamedParameter($IP)))
                ->execute();

            while ($row = $statement->fetch()) {
                return $row['iso2'];
            }
        } else {
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('iso2', 'tx_locate_ip2country',
                'ipfrom <= ' . $IP . ' AND ipto >= ' . $IP);
            if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                return $row['iso2'];
            }
        }

        return false;
    }

}
