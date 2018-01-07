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

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\AbstractFunctionalTestCase;
use App\Tests\Functional\HelperTrait;

class FeedControllerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testFeedHasItems(): void
    {
        $client = static::createDefaultClient();
        $this->createLinks($client, 5);

        $crawler = $client->request('GET', $this->generateUrl($client, 'app_feed_default'));
        $this->assertEquals(5, $crawler->filter('rss channel item')->count());
    }
}
