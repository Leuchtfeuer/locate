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
