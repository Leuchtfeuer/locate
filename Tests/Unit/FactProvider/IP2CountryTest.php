<?php
declare(strict_types=1);
namespace Leuchtfeuer\Locate\Tests\Unit\FactProvider;

use Leuchtfeuer\Locate\FactProvider\IP2Country;
use PHPUnit\Framework\TestCase;

class IP2CountryTest extends TestCase
{
    /**
     * @test
     */
    public function askingForIsGuiltyTwiceReturnsCorrectState(): void
    {
        $subject = new IP2Country('dummy');
        $classReflection = new \ReflectionClass($subject);
        $reflectionProperty = $classReflection->getProperty('facts');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($subject, [IP2Country::PROVIDER_NAME => 'en']);

        self::assertFalse($subject->isGuilty('de'));
        self::assertTrue($subject->isGuilty('en'));
    }
}
