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

namespace App\Manager;

use App\Entity\Link;
use App\Exception\LinkNotFoundException;
use App\Exception\NoFreeNameException;
use App\Helper\NameGenerator;
use App\Service\ContextService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class LinkManager
{
    private $entityManager;

    private $contextService;

    public function __construct(EntityManagerInterface $entityManager, ContextService $contextService)
    {
        $this->entityManager = $entityManager;
        $this->contextService = $contextService;
    }

    public function create(bool $reuse, string $url): Link
    {
        if (true === $reuse) {
            $link = $this->entityManager->getRepository(Link::class)->findOneBy(
                ['url' => $url]
            );

            if (null !== $link) {
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

        if (null === $link) {
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

            if (null === $link) {
                return $name;
            }
        }

        throw new NoFreeNameException();
    }
}
