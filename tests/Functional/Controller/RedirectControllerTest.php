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

namespace Tests\Functional\Controller;

use AppBundle\Entity\Link;
use AppBundle\Entity\Site;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\AbstractFunctionalTestCase;
use Tests\Functional\HelperTrait;

class RedirectControllerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testRedirectToOriginalSite(): void
    {
        $client = static::createDefaultClient();
        $link = $this->createLink($client);

        $client->request('GET', '/' . $link->getName());
        $response = $client->getResponse();

        if (!($response instanceof RedirectResponse)) {
            $this->fail('Expected redirection');
        } else {
            $this->assertEquals($link->getUrl(), $response->getTargetUrl());
        }
    }

    public function testLinkIsCaseSensitive(): void
    {
        $client = static::createDefaultClient();
        $entityManager = $this->getEntityManager($client);
        $site = $entityManager->getRepository(Site::class)->getDefault();

        $link = new Link($site);
        $link
            ->setName('YYYYY')
            ->setUrl('http://www.example.com/');

        $entityManager->persist($link);
        $entityManager->flush();

        $client->request('GET', '/' . strtolower($link->getName()));

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }
}
