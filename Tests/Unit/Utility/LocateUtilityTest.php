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

namespace Leuchtfeuer\Locate\Tests\Unit\Utility;

use Leuchtfeuer\Locate\Utility\LocateUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LocateUtilityTest extends UnitTestCase
{
    public static function numericIpDataProvider(): array
    {
        return [
            'IPv4' => ['1.0.0.1', '16777217'],
            'highest IPv4' => ['255.255.255.255', '4294967295'],
            'unspecified IPv6' => ['::', '0'],
            // Both numbers are equal as floats
            'IPv6 last address of a range' => ['2A02:8108:8400:23D8:5C6C:DA81:E68D:5CAF', '55840577522199898067217455099144920239'],
            'IPv6 first address of the following range' => ['2A02:8108:8400:23D8:5C6C:DA81:E68D:5CB0', '55840577522199898067217455099144920240'],
            'highest IPv6' => ['ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff', '340282366920938463463374607431768211455'],
            'IPv4-mapped IPv6' => ['::ffff:1.0.0.1', '16777217'],
            'IPv4-mapped IPv6 in upper case' => ['::FFFF:1.0.0.1', '16777217'],
            'IPv4-mapped IPv6, expanded' => ['0:0:0:0:0:ffff:1.0.0.1', '16777217'],
            'IPv4-mapped IPv6 in hexadecimal notation' => ['::ffff:c000:201', '3221225985'],
        ];
    }

    public static function invalidIpDataProvider(): array
    {
        return [
            'empty' => [''],
            'IPv4 out of range' => ['1.0.0.256'],
            'IPv4 with leading zero' => ['01.0.0.1'],
            'list of addresses' => ['1.0.0.1, 1.0.4.1'],
            'IPv4-mapped IPv6 with invalid IPv4' => ['::ffff:1.0.0'],
            'IPv6 with zone' => ['fe80::1%eth0'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidIpDataProvider
     */
    public function getNumericIpReturnsFalseForInvalidIp(string $ip): void
    {
        self::assertFalse((new LocateUtility())->getNumericIp($ip));
    }

    /**
     * @test
     * @dataProvider numericIpDataProvider
     */
    public function getNumericIpReturnsExactDecimalNumber(string $ip, string $expected): void
    {
        self::assertSame($expected, (new LocateUtility())->getNumericIp($ip));
    }
}
