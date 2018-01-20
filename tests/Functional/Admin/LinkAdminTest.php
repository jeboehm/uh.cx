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

namespace App\Tests\Functional\Admin;

use App\Entity\Link;
use App\Helper\NameGenerator;
use App\Tests\Functional\AbstractFunctionalTestCase;
use App\Tests\Functional\HelperTrait;
use Symfony\Component\HttpFoundation\Response;

class LinkAdminTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testAdminListRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->generateUrl($client, 'admin_app_link_list'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @covers \App\Subscriber\Doctrine\ContextFilterSubscriber::onKernelRequest()
     */
    public function testLinkAdminListShowsAllSitesLinks(): void
    {
        $client = static::createClient();
        $site = static::createSite($client, 'random.test', 'preview.random.test', 'TestEnv2');
        $this->createLink($client);

        $entityManager = $this->getEntityManager($client);
        $link = new Link($site);
        $link->setUrl('http://example.com/' . NameGenerator::generate(5));
        $link->setName(NameGenerator::generate(5));

        $entityManager->persist($link);
        $entityManager->flush();

        $crawler = $client->request('GET', $this->generateUrl($client, 'admin_app_link_list'));
        $filter = $crawler->filter('td');
        $values = [];

        foreach ($filter as $node) {
            $values[] = trim($node->nodeValue);
        }

        $this->assertContains('TestEnv2', $values);
        $this->assertContains('Testenvironment', $values);
    }
}
