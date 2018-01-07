<?php

declare(strict_types=1);
/**
 * This file is part of the uh.cx package.
 *
 * (c) Jeffrey Boehm <https://github.com/jeboehm>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Unit\Helper;

use App\Helper\NameGenerator;
use PHPUnit\Framework\TestCase;

class NameGeneratorTest extends TestCase
{
    public function testGenerateNameWithoutCharsSpecified(): void
    {
        $name = NameGenerator::generate(8);

        $this->assertEquals(8, strlen($name));
    }
}
