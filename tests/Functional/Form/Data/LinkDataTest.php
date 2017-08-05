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

namespace Tests\Functional\Form\Data;

use AppBundle\Form\Data\LinkData;
use Tests\Functional\AbstractFunctionalTestCase;

class LinkDataTest extends AbstractFunctionalTestCase
{
    /**
     * @dataProvider dataProviderForTestUrlValidation
     */
    public function testUrlValidation(string $url, bool $expect): void
    {
        $client = static::createDefaultClient();
        $validator = $client->getContainer()->get('validator');

        $model = new LinkData();
        $model->setUrl($url);

        $violations = $validator->validate($model);

        $this->assertEquals($expect, $violations->count() === 0);
    }

    public function dataProviderForTestUrlValidation(): array
    {
        return [
            ['ftp://example.com/', false],
            ['http://example.com/', true],
            ['http://' . self::HOST_DEFAULT, false],
            ['http://' . self::HOST_UNKNOWN, true],
            ['http://' . self::HOST_PREVIEW, false],
        ];
    }
}
