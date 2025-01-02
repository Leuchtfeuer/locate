<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\EventListener;

use Doctrine\DBAL\Exception;
use Leuchtfeuer\Locate\Utility\TypeCaster;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\Event\ModifyRecordOverlayIconIdentifierEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class ModifyRecordOverlayIconIdentifierEventListener
{
    /**
     * @throws Exception
     */
    #[AsEventListener(
        identifier: 'locate/modify-record-overlay-icon-identifier',
        event: ModifyRecordOverlayIconIdentifierEvent::class
    )]
    public function __invoke(ModifyRecordOverlayIconIdentifierEvent $event): void
    {
        $table = $event->getTable();
        $row = $event->getRow();
        $iconName = $event->getOverlayIconIdentifier();

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

        $event->setOverlayIconIdentifier($iconName);
    }

    /**
     * @param array<string, mixed> $row
     * @throws Exception
     */
    private function countRegions(string $table, array $row): int
    {
        if (empty($row['uid'])) {
            return 0;
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        return TypeCaster::toInt($qb
            ->select('tx_locate_regions')
            ->from($table)->where($qb->expr()->eq('uid', TypeCaster::toInt($row['uid'])))->executeQuery()
            ->fetchOne());
    }
}
