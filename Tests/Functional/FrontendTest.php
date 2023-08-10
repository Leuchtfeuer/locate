<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Tests\Functional;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class FrontendTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/locate'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/Fixtures/redirect-scenario.xml');
    }

    /**
     * @test
     */
    public function redirectToMainlandChinaFromIpAddress(): void
    {
        $this->setUpFrontendRootPage(
            1,
            ['setup' => ['EXT:locate/Tests/Functional/Fixtures/TypoScript/setup.typoscript']]
        );
        $this->setUpFrontendSite(1);
        $response = $this->executeFrontendRequest(
            (new InternalRequest())
                ->withPageId(1)
                ->withHeader('Accept-Language', 'en-GB,en-US;q=0.9,en;q=0.8'),
            (new InternalRequestContext())->withGlobalSettings([
                '_SERVER' => [
                    'REMOTE_ADDR' => '1.207.255.250',
                ],
            ])
        );
        self::assertSame('Hello World', (string)$response->getBody());
    }

    /**
     * Copied from \TYPO3\CMS\Core\Tests\Functional\DataHandling\AbstractDataHandlerActionTestCase::setUpFrontendSite
     * in typo3/cms-core
     *
     * Create a simple site config for the tests that
     * call a frontend page.
     *
     * @param int $pageId
     * @param array $additionalLanguages
     */
    protected function setUpFrontendSite(int $pageId, array $additionalLanguages = []): void
    {
        $languages = [
            0 => [
                'title' => 'English',
                'enabled' => true,
                'languageId' => 0,
                'base' => '/',
                'typo3Language' => 'default',
                'locale' => 'en_US.UTF-8',
                'iso-639-1' => 'en',
                'navigationTitle' => '',
                'hreflang' => '',
                'direction' => '',
                'flag' => 'us',
            ],
            1 => [
                'title' => 'ZN',
                'enabled' => true,
                'languageId' => 1,
                'base' => '/zn',
                'typo3Language' => 'default',
                'locale' => 'zn_ZN.UTF-8',
                'iso-639-1' => 'zn',
                'navigationTitle' => '',
                'hreflang' => '',
                'direction' => '',
                'flag' => 'zn',
            ],
        ];
        $languages = array_merge($languages, $additionalLanguages);
        $configuration = [
            'rootPageId' => $pageId,
            'base' => '/',
            'languages' => $languages,
            'errorHandling' => [],
            'routes' => [],
        ];
        GeneralUtility::mkdir_deep($this->instancePath . '/typo3conf/sites/testing/');
        $yamlFileContents = Yaml::dump($configuration, 99, 2);
        $fileName = $this->instancePath . '/typo3conf/sites/testing/config.yaml';
        GeneralUtility::writeFile($fileName, $yamlFileContents);
        // Ensure that no other site configuration was cached before
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('core');
        if ($cache->has('sites-configuration')) {
            $cache->remove('sites-configuration');
        }
    }
}
