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

namespace App\Tests\Functional\Manager;

use App\Entity\Hit;
use App\Manager\HitManager;
use App\Tests\Functional\AbstractFunctionalTestCase;
use App\Tests\Functional\HelperTrait;

class HitManagerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testGetHitsSumsUpAllHits(): void
    {
        $client = static::createDefaultClient();
        $manager = $client->getContainer()->get('test.' . HitManager::class);
        $link1 = $this->createLink($client);
        $link2 = $this->createLink($client);

        $client->request('GET', '/' . $link1->getName());
        $client->request('GET', '/' . $link1->getName());
        $client->request('GET', $this->generateUrl($client, 'app_info_info', ['name' => $link1->getName()]));
        $client->request('GET', $this->generateUrl($client, 'app_info_info', ['name' => $link2->getName()]));

        $client->getContainer()->get('request_stack')->push($client->getRequest());

        $expect1 = [
            Hit::TYPE_INFO => 1,
            Hit::TYPE_REDIRECT => 2,
        ];

        $expect2 = [
            Hit::TYPE_INFO => 1,
        ];

        $this->assertEquals($expect1, $manager->getHits($link1));
        $this->assertEquals($expect2, $manager->getHits($link2));
    }
}
