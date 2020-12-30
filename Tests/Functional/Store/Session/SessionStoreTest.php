<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Tests\Functional\Store\Session;

use Leuchtfeuer\Locate\Store\SessionStore;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Leuchtfeuer\Locate\Store\SessionStore
 */
class SessionStoreTest extends FunctionalTestCase
{
    /**
     * @var SessionStore
     */
    protected $subject;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/locate'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new SessionStore();
    }

    /**
     * @test
     */
    public function storeDataTest()
    {
        $this->subject->set('foo', 'bar');
        self::assertSame('bar', $this->subject->get('foo'));
    }
}
