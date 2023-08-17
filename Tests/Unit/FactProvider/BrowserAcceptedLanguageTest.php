<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Tests\Unit\FactProvider;

use Leuchtfeuer\Locate\FactProvider\BrowserAcceptedLanguage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class BrowserAcceptedLanguageTest extends UnitTestCase
{
    /**
     * @test
     */
    public function askingForIsGuiltyTwiceReturnsCorrectState(): void
    {
        $subject = $this->getAccessibleMock(
            BrowserAcceptedLanguage::class,
            null,
            [],
            '',
            false
        );
        $subject->_set('facts', [
            'en' => 1,
        ]);

        self::assertTrue($subject->isGuilty('en'));
        self::assertFalse($subject->isGuilty('de'));
    }

    /**
     * @test
     */
    public function askingForIsGuiltyThreeTimesForMultipleLanguagesReturnsCorrectState(): void
    {
        $subject = $this->getAccessibleMock(
            BrowserAcceptedLanguage::class,
            null,
            [],
            '',
            false
        );
        $subject->_set('facts', [
            'de' => 12,
            'en' => 1,
        ]);

        self::assertTrue($subject->isGuilty('en'));
        self::assertTrue($subject->isGuilty('de'));
        self::assertFalse($subject->isGuilty('fr'));
    }
}
