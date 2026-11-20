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

namespace Leuchtfeuer\Locate\Tests\Unit\FactProvider;

use Leuchtfeuer\Locate\FactProvider\IP2Country;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class IP2CountryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function askingForIsGuiltyTwiceReturnsCorrectState(): void
    {
        $subject = $this->getAccessibleMock(
            IP2Country::class,
            null,
            [],
            '',
            false
        );
        $subject->_set('facts', [IP2Country::PROVIDER_NAME => 'en']);

        self::assertFalse($subject->isGuilty('de'));
        self::assertTrue($subject->isGuilty('en'));
    }
}
