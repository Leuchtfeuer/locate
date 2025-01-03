<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Tests\Functional\Store\Session;

use Leuchtfeuer\Locate\Store\SessionStore;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Leuchtfeuer\Locate\Store\SessionStore
 */
class SessionStoreTest extends FunctionalTestCase
{
    protected SessionStore $subject;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'typo3conf/ext/locate',
        ];

        parent::setUp();

        $this->subject = new SessionStore();
    }

    /**
     * @test
     */
    public function getSessionKeyTest(): void
    {
        $sessionKeyName = $this->subject->getSessionKeyName('foo');
        self::assertSame(SessionStore::SESSION_BASE_NAME . 'foo', $sessionKeyName);
    }

    /**
     * @test
     */
    public function storeDataTest(): void
    {
        $this->subject->set('foo', 'bar');
        self::assertSame('bar', $this->subject->get('foo'));
    }

    /**
     * @test
     */
    public function deleteDataTest(): void
    {
        $this->subject->set('foo', 'bar');
        $this->subject->delete('foo');
        self::assertNull($this->subject->get('foo'));
        self::assertSame('biz', $this->subject->get('foo', 'biz'));
    }

    /**
     * @test
     */
    public function createNewStoreTest(): void
    {
        $sessionBaseName = 'test_key_';
        $sessionKeyName = (new SessionStore($sessionBaseName))->getSessionKeyName('foo');
        self::assertSame($sessionBaseName . 'foo', $sessionKeyName);
    }
}
