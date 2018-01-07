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

use App\Entity\Link;
use App\Entity\Site;
use App\Helper\NameGenerator;
use App\Manager\LinkManager;
use App\Tests\Functional\AbstractFunctionalTestCase;
use App\Tests\Functional\HelperTrait;

class LinkManagerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testReuseLink(): void
    {
        $client = static::createDefaultClient();

        $link = $this->createLink($client);
        $manager = $client->getContainer()->get('test.' . LinkManager::class);

        $client->request('GET', '/');
        $client->getContainer()->get('request_stack')->push($client->getRequest());

        $this->assertEquals($link->getName(), $manager->create(true, $link->getUrl())->getName());
    }

    public function testReuseLinkIsCaseSensitive(): void
    {
        $client = static::createDefaultClient();

        $entityManager = $this->getEntityManager($client);
        $site = $entityManager->getRepository(Site::class)->getDefault();
        $manager = $client->getContainer()->get('test.' . LinkManager::class);

        $client->request('GET', '/');
        $client->getContainer()->get('request_stack')->push($client->getRequest());

        $link = new Link($site);
        $link
            ->setName(NameGenerator::generate(5))
            ->setUrl('http://example.com/AAAAA');
        $entityManager->persist($link);
        $entityManager->flush();

        $generatedLink = $manager->create(true, 'http://example.com/aaaaa');

        $this->assertNotEquals($link->getName(), $generatedLink->getName());
    }
}
