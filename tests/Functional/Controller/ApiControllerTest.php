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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\AbstractFunctionalTestCase;
use Tests\Functional\HelperTrait;

class ApiControllerTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testCreateLink(): void
    {
        $request = ['url' => 'http://example.com/', 'reuse' => false];
        $client = static::createDefaultClient();
        $client->request('POST', $this->generateUrl($client, 'app_api_create'), [], [], [], json_encode($request));

        /** @var JsonResponse $response */
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $decoded = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('UrlDirect', $decoded);
        $this->assertArrayHasKey('UrlPreview', $decoded);
        $this->assertArrayHasKey('UrlOriginal', $decoded);
        $this->assertArrayHasKey('QrDirect', $decoded);
        $this->assertArrayHasKey('QrPreview', $decoded);

        $this->assertStringStartsWith('http://' . static::HOST_DEFAULT, $decoded['UrlDirect']);
        $this->assertStringStartsWith('http://' . static::HOST_PREVIEW, $decoded['UrlPreview']);
        $this->assertEquals('http://example.com/', $decoded['UrlOriginal']);
        $this->assertStringStartsWith('https://chart.apis.google.com/', $decoded['QrDirect']);
        $this->assertStringStartsWith('https://chart.apis.google.com/', $decoded['QrPreview']);
    }

    public function testReuseLink(): void
    {
        $request = ['url' => 'http://example.com/', 'reuse' => true];
        $client = static::createDefaultClient();
        $client->request('POST', $this->generateUrl($client, 'app_api_create'), [], [], [], json_encode($request));

        $decoded1 = json_decode($client->getResponse()->getContent(), true);

        $client->request('POST', $this->generateUrl($client, 'app_api_create'), [], [], [], json_encode($request));
        $decoded2 = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals($decoded1, $decoded2);
    }

    public function testInvalidPostRequest(): void
    {
        $request = 'huhu';
        $client = static::createDefaultClient();
        $client->request('POST', $this->generateUrl($client, 'app_api_create'), [], [], [], json_encode($request));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testValidRequestButInvalidUrl(): void
    {
        $request = ['url' => 'ftp://example.com/', 'reuse' => true];
        $client = static::createDefaultClient();
        $client->request('POST', $this->generateUrl($client, 'app_api_create'), [], [], [], json_encode($request));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals('Please specify a valid link.', $client->getResponse()->getContent());
    }

    public function testInvalidGetRequest(): void
    {
        $client = static::createDefaultClient();
        $client->request('GET', $this->generateUrl($client, 'app_api_create'));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testBackwardCompatibilityForCreate(): void
    {
        $request = ['url' => 'http://example.com/', 'reuse' => false];
        $client = static::createDefaultClient();
        $client->request(
            'POST',
            $this->generateUrl($client, 'app_api_compat_create'),
            [],
            [],
            [],
            json_encode($request)
        );

        /** @var JsonResponse $response */
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('UrlDirect', $decoded);
    }
}
