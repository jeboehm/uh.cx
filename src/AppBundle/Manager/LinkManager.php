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

namespace AppBundle\Manager;

use AppBundle\Entity\Link;
use AppBundle\Exception\LinkNotFoundException;
use AppBundle\Exception\NoFreeNameException;
use AppBundle\Helper\NameGenerator;
use AppBundle\Service\ContextService;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class LinkManager
{
    private $entityManager;

    private $contextService;

    public function __construct(EntityManager $entityManager, ContextService $contextService)
    {
        $this->entityManager = $entityManager;
        $this->contextService = $contextService;
    }

    public function create(bool $reuse, string $url): Link
    {
        if ($reuse === true) {
            $link = $this->entityManager->getRepository(Link::class)->findOneBy(
                ['url' => $url]
            );

            if ($link !== null) {
                return $link;
            }
        }

        $link = new Link($this->contextService->getContext()->getSite());
        $link->setName($this->getFreeName());
        $link->setUrl($url);
        $link->setAddedBy($this->contextService->getContext()->getClientIp());

        $this->entityManager->persist($link);
        $this->entityManager->flush();

        return $link;
    }

    public function get(string $name): Link
    {
        $repository = $this->entityManager->getRepository(Link::class);

        if (empty(trim($name))) {
            throw new InvalidArgumentException();
        }

        $link = $repository->findOneBy(['name' => $name]);

        if ($link === null) {
            throw new LinkNotFoundException();
        }

        return $link;
    }

    private function getFreeName(): string
    {
        $repository = $this->entityManager->getRepository(Link::class);

        foreach (range(0, 5) as $i) {
            $name = NameGenerator::generate(5);
            $link = $repository->findOneBy(['name' => $name]);

            if ($link === null) {
                return $name;
            }
        }

        throw new NoFreeNameException();
    }
}
