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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Leuchtfeuer\Locate\Utility\LocateUtility
 */
class LocateUtilityTest extends FunctionalTestCase
{
    protected LocateUtility $subject;

    private array $serverBackup;

    private array $systemConfigurationBackup;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'typo3conf/ext/locate',
        ];

        parent::setUp();

        $this->subject = new LocateUtility();
        $this->serverBackup = $_SERVER;
        $this->systemConfigurationBackup = $GLOBALS['TYPO3_CONF_VARS']['SYS'];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        $GLOBALS['TYPO3_CONF_VARS']['SYS'] = $this->systemConfigurationBackup;
        GeneralUtility::flushInternalRuntimeCaches();

        parent::tearDown();
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
            '::ffff:127.0.0.1' => '2130706433',
        ];

        foreach ($ips as $ip => $long) {
            self::assertSame($long, $this->subject->getNumericIp($ip));
        }
    }

    public static function countryIso2FromIpDataProvider(): array
    {
        return [
            'IPv4 first address of range' => ['1.0.0.0', 'US'],
            'IPv4 inside range' => ['1.0.0.1', 'US'],
            'IPv4 last address of range' => ['1.0.0.255', 'US'],
            'IPv4 between ranges' => ['1.0.1.0', false],
            'IPv4 last address of last range' => ['1.0.7.255', 'AU'],
            'IPv4 above last range' => ['1.0.8.0', false],
            'IPv4 reserved range' => ['0.0.0.1', '-'],
            // Both numbers are equal as floats, a lossy comparison would mix up the ranges
            'IPv6 last address of range' => ['2A02:8108:8400:23D8:5C6C:DA81:E68D:5CAF', 'DE'],
            'IPv6 first address of following range' => ['2A02:8108:8400:23D8:5C6C:DA81:E68D:5CB0', 'AT'],
            'IPv6 above last range' => ['2A02:8108:8400:23D8:5C6C:DA81:E68D:5D00', false],
            'IPv4-mapped IPv6' => ['::ffff:1.0.0.1', 'US'],
            'IPv4-mapped IPv6 in upper case' => ['::FFFF:1.0.4.1', 'AU'],
            'list of addresses' => ['1.0.0.1, 1.0.4.1', false],
            'invalid IPv4' => ['1.0.0.256', false],
        ];
    }

    /**
     * @test
     * @dataProvider countryIso2FromIpDataProvider
     */
    public function getCountryIso2FromIpReturnsCountryOfRangeContainingIp(string $ip, string|false $expected): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/ip2country.csv');

        self::assertSame($expected, $this->subject->getCountryIso2FromIP($ip));
    }

    /**
     * @test
     */
    public function forwardedForHeaderIsIgnoredWithoutReverseProxyConfiguration(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/ip2country.csv');
        $_SERVER['REMOTE_ADDR'] = '1.0.4.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.0.0.1';
        GeneralUtility::flushInternalRuntimeCaches();

        self::assertSame('AU', $this->subject->getCountryIso2FromIP());
    }

    /**
     * @test
     */
    public function forwardedForHeaderIsUsedForConfiguredReverseProxy(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/ip2country.csv');
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'] = '1.0.4.1';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyHeaderMultiValue'] = 'last';
        $_SERVER['REMOTE_ADDR'] = '1.0.4.1';
        // The proxy appends the client address, the first entry was sent by the client
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.0.7.1, 1.0.0.1';
        GeneralUtility::flushInternalRuntimeCaches();

        self::assertSame('US', $this->subject->getCountryIso2FromIP());
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
            'de-de',
        ];

        foreach ($values as $value) {
            LocateUtility::mainstreamValue($value);
            self::assertSame('de_de', $value);
        }
    }
}
