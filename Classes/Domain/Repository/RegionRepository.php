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

namespace Leuchtfeuer\Locate\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RegionRepository
{
    public const int APPLY_WHEN_NO_IP_MATCHES = -1;

    /**
     * @return array<null>|array<string, bool>
     * @throws Exception
     */
    public function getCountriesForPage(int $id): array
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_locate_page_region_mm');
        $iso2Codes = [];

        $results = $qb
            ->select('c.cn_iso_2')
            ->from('tx_locate_page_region_mm', 'pmm')
            ->join('pmm', 'tx_locate_domain_model_region', 'r', 'r.uid = pmm.uid_foreign')
            ->join('r', 'tx_locate_region_country_mm', 'rmm', 'rmm.uid_local = r.uid')
            ->join('rmm', 'static_countries', 'c', 'c.uid = rmm.uid_foreign')->where($qb->expr()->eq('pmm.uid_local', $id))->executeQuery()
            ->fetchAllAssociative();

        foreach ($results as $result) {
            $iso2Codes[$result['cn_iso_2']] = true;
        }

        return $iso2Codes;
    }

    /**
     * @throws Exception
     */
    public function shouldApplyWhenNoIpMatches(int $id): bool
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_locate_page_region_mm');

        $results = $qb
            ->select('*')
            ->from('tx_locate_page_region_mm')
            ->where($qb->expr()->eq('uid_local', $id))->andWhere($qb->expr()->eq('uid_foreign',self::APPLY_WHEN_NO_IP_MATCHES))->executeQuery()
            ->fetchAllAssociative();

        return !empty($results);
    }
}
