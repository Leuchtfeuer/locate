<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Hook;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OverrideIconOverlayHook
{
    /**
     * @throws Exception
     */
    public function postOverlayPriorityLookup(string $table, array $row, array $status, string $iconName): string
    {
        if ($table === 'pages' && !empty($row) && !str_contains((string)$row['uid'], 'NEW')) {
            // since tx_locate_regions is not included in the row array (PageTreeRepository is initialized with empty additionalFields)
            // we need to get the necessary information on our own
            $regions = $this->countRegions($table, $row);

            if ($regions > 0) {
                switch ($iconName) {
                    // TODO: Support this case and add dedicated overlay icon (also for other overlay icons)
//                    case 'overlay-restricted':
//                        $iconName = 'apps-pagetree-page-frontend-user-root';
//                        break;

                    default:
                        $iconName = 'overlay-translated';
                }
            }
        }

        return $iconName;
    }

    /**
     * @throws Exception
     */
    private function countRegions(string $table, array $row): int
    {
        if (empty($row['uid'])) {
            return 0;
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        return (int)$qb
            ->select('tx_locate_regions')
            ->from($table)->where($qb->expr()->eq('uid', $row['uid']))->executeQuery()
            ->fetchOne();
    }
}
