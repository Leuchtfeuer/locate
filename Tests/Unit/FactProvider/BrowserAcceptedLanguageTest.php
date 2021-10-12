<?php
declare(strict_types=1);
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
