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

use AppBundle\Entity\Hit;
use AppBundle\Entity\Link;
use AppBundle\Service\ContextService;
use Doctrine\ORM\EntityManager;

class HitManager
{
    private $entityManager;

    private $contextService;

    public function __construct(EntityManager $entityManager, ContextService $contextService)
    {
        $this->entityManager = $entityManager;
        $this->contextService = $contextService;
    }

    public function addHit(Link $link, int $type): void
    {
        $hit = new Hit($link);
        $hit
            ->setType($type)
            ->setReferer($this->contextService->getContext()->getReferer())
            ->setUserAgent($this->contextService->getContext()->getUserAgent())
            ->setVisitedBy($this->contextService->getContext()->getClientIp());

        $this->entityManager->persist($hit);
        $this->entityManager->flush();
    }

    public function getHits(Link $link): array
    {
        $repository = $this->entityManager->getRepository(Hit::class);

        return $repository->getHitsGroupedByType($link);
    }
}
