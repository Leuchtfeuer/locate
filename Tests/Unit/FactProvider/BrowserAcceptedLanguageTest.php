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
use PHPUnit\Framework\TestCase;

class BrowserAcceptedLanguageTest extends TestCase
{
    /**
     * @test
     */
    public function askingForIsGuiltyTwiceReturnsCorrectState(): void
    {
        $subject = new BrowserAcceptedLanguage('dummy');
        $classReflection = new \ReflectionClass($subject);
        $reflectionProperty = $classReflection->getProperty('facts');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($subject, [
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
        $subject = new BrowserAcceptedLanguage('dummy');
        $classReflection = new \ReflectionClass($subject);
        $reflectionProperty = $classReflection->getProperty('facts');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($subject, [
            'de' => 12,
            'en' => 1,
        ]);

        self::assertTrue($subject->isGuilty('en'));
        self::assertTrue($subject->isGuilty('de'));
        self::assertFalse($subject->isGuilty('fr'));
    }
}
