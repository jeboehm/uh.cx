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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\AbstractFunctionalTestCase;
use Tests\Functional\HelperTrait;

class DefaultControllerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testIndex(): void
    {
        $client = static::createDefaultClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'app_default_default'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('Testenvironment', $crawler->filter('.container h3')->text());
    }

    public function testUnknownSiteIsRedirectedToDefault(): void
    {
        $client = static::createUnknownClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'app_default_default'));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('Testenvironment', $crawler->filter('.container h3')->text());
    }

    public function testSendFormWithInvalidData(): void
    {
        $client = static::createDefaultClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'app_default_default'));
        $form = $crawler->selectButton('link_create')->form();
        $form['link[url]'] = 'ftp://example.com/';

        $crawler = $client->submit($form);
        $this->assertContains('Please specify a valid', $crawler->filter('.container .alert')->text());
    }

    public function testSendFormWithValidData(): void
    {
        $client = static::createDefaultClient();
        $crawler = $client->request('GET', $this->generateUrl($client, 'app_default_default'));
        $form = $crawler->selectButton('link_create')->form();
        $form['link[url]'] = 'http://example.com/';

        $client->submit($form);
        $response = $client->getResponse();

        if (!($response instanceof RedirectResponse)) {
            $this->fail('Expected redirection');
        } else {
            $this->assertStringStartsWith('/info/', $response->getTargetUrl());
        }
    }

    public function testLinkCreationViaToolbarButton(): void
    {
        $client = static::createDefaultClient();
        $client->request(
            'GET',
            $this->generateUrl($client, 'app_default_default'),
            ['tb' => 'http://www.example.com/']
        );

        $response = $client->getResponse();

        if (!($response instanceof RedirectResponse)) {
            $this->fail('Expected redirection');
        } else {
            $this->assertStringStartsWith('/info/', $response->getTargetUrl());
        }
    }

    public function testLinkCreationViaToolbarButtonFailsWithInvalidUrl(): void
    {
        $client = static::createDefaultClient();
        $crawler = $client->request(
            'GET',
            $this->generateUrl($client, 'app_default_default'),
            ['tb' => 'ftp://example.com']
        );
        $this->assertContains('Please specify a valid', $crawler->filter('.container .alert')->text());
    }
}
