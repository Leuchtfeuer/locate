<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Tests\Functional\Utility;

use Leuchtfeuer\Locate\Utility\LocateUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Leuchtfeuer\Locate\Utility\LocateUtility
 */
class LocateUtilityTest extends FunctionalTestCase
{
    protected $subject;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/locate'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new LocateUtility();
    }

    /**
     * @test
     */
    public function getNumericIPTest()
    {
        $ips = [
            '2A02:8108:8400:23D8:5C6C:DA81:E68D:5CAF' => '55840577522199898067217455099144920239',
            '127.0.0.1' => '2130706433',
            '192.168.178.1' => '3232281089',
        ];

        foreach ($ips as $ip => $long) {
            self::assertSame($long, $this->subject->getNumericIp($ip));
        }
    }

    /**
     * @test
     */
    public function transformValuesTest()
    {
        $values = [
            'de-DE',
            'de_DE',
            'de_de',
            'de-de'
        ];

        foreach ($values as $value) {
            LocateUtility::mainstreamValue($value);
            self::assertSame('de_de', $value);
        }
    }
}
