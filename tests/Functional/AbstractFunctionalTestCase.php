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

namespace App\Tests\Functional;

use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractFunctionalTestCase extends WebTestCase
{
    const HOST_DEFAULT = 'test.dev';
    const HOST_PREVIEW = 'preview.test.dev';
    const HOST_UNKNOWN = 'unknown.test.dev';

    final public static function createDefaultClient(): Client
    {
        return static::createClient([], ['HTTP_HOST' => self::HOST_DEFAULT]);
    }

    final public static function createPreviewClient(): Client
    {
        return static::createClient([], ['HTTP_HOST' => self::HOST_PREVIEW]);
    }

    final public static function createUnknownClient(): Client
    {
        return static::createClient([], ['HTTP_HOST' => self::HOST_UNKNOWN]);
    }

    final protected static function createClient(array $options = [], array $server = []): Client
    {
        $client = parent::createClient($options, $server);

        static::prepareEnvironment($client);

        return $client;
    }

    final protected static function createSite(Client $client, string $host, string $previewHost, string $name): Site
    {
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $site = new Site();
        $site
            ->setHost($host)
            ->setPreviewHost($previewHost)
            ->setSecure(false)
            ->setTest(false)
            ->setDefault(true)
            ->setName($name);

        $entityManager->persist($site);
        $entityManager->flush();

        return $site;
    }

    private static function prepareEnvironment(Client $client): void
    {
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $sites = $entityManager->getRepository(Site::class)->findAll();

        foreach ($sites as $site) {
            $entityManager->remove($site);
        }

        $entityManager->flush();
        $entityManager->clear();

        static::createSite($client, self::HOST_DEFAULT, self::HOST_PREVIEW, 'Testenvironment');
        $entityManager->clear();
    }
}
