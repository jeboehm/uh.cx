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

use App\Entity\Link;
use App\Entity\Site;
use App\Tests\Functional\AbstractFunctionalTestCase;
use App\Tests\Functional\HelperTrait;
use Symfony\Component\HttpFoundation\Response;

class InfoControllerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testInfoAction(): void
    {
        $client = static::createDefaultClient();
        $link = $this->createLink($client);

        $crawler = $client->request('GET', $this->generateUrl($client, 'app_info_info', ['name' => $link->getName()]));
        $url = $crawler->filter('#link-short')->attr('value');

        $this->assertEquals(sprintf('http://%s/%s', self::HOST_DEFAULT, $link->getName()), $url);
    }

    public function testPreviewAction(): void
    {
        $client = static::createDefaultClient();
        $link = $this->createLink($client);

        $crawler = $client->request(
            'GET',
            $this->generateUrl($client, 'app_info_preview', ['name' => $link->getName()])
        );
        $url = $crawler->filter('pre')->text();

        $this->assertEquals($link->getUrl(), $url);
    }

    public function testPreviewWithUnknownLink(): void
    {
        $client = static::createDefaultClient();
        $client->request(
            'GET',
            $this->generateUrl($client, 'app_info_preview', ['name' => 'XXXXX'])
        );
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testDifferentSitesHaveSeparatedLinks(): void
    {
        $client = static::createDefaultClient();
        $entityManager = $this->getEntityManager($client);

        $site = new Site();
        $site
            ->setName('test')
            ->setDefault(false)
            ->setHost('xxx.xyz')
            ->setPreviewHost('yyy.xyz')
            ->setSecure(false)
            ->setTest(true);

        $entityManager->persist($site);
        $entityManager->flush();

        $link = new Link($site);
        $link
            ->setName('abcde')
            ->setUrl('http://www.example.com/');

        $entityManager->persist($link);
        $entityManager->flush();

        $ownLink = $this->createLink($client);

        $client->request(
            'GET',
            $this->generateUrl($client, 'app_info_info', ['name' => $ownLink->getName()])
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->generateUrl($client, 'app_info_info', ['name' => $link->getName()])
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
