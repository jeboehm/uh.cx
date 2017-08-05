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

namespace Tests\Functional;

use AppBundle\Entity\Link;
use AppBundle\Entity\Site;
use AppBundle\Helper\NameGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;

trait HelperTrait
{
    final public function generateUrl(Client $client, string $route, array $parameters = []): string
    {
        return $client->getContainer()->get('router')->generate($route, $parameters);
    }

    final public function createLinks(Client $client, int $amount = 5): array
    {
        $links = [];

        for ($i = 0; $i < $amount; ++$i) {
            $links[] = $this->createLink($client);
        }

        return $links;
    }

    final public function createLink(Client $client): Link
    {
        $entityManager = $this->getEntityManager($client);
        $site = $entityManager->getRepository(Site::class)->getDefault();
        $link = new Link($site);
        $link->setUrl('http://example.com/' . NameGenerator::generate(5));
        $link->setName(NameGenerator::generate(5));

        $entityManager->persist($link);
        $entityManager->flush();

        return $link;
    }

    final public function getEntityManager(Client $client): EntityManager
    {
        return $client->getContainer()->get('doctrine.orm.entity_manager');
    }
}
