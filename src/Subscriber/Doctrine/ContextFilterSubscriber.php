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

namespace App\Subscriber\Doctrine;

use App\Service\ContextService;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ContextFilterSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    private $contextService;

    public function __construct(EntityManagerInterface $entityManager, ContextService $contextService)
    {
        $this->entityManager = $entityManager;
        $this->contextService = $contextService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(): void
    {
        if (!$this->contextService->getContext()->isAdmin()) {
            $this->enableContextFilter();
        }
    }

    private function enableContextFilter(): void
    {
        try {
            $filter = $this->entityManager->getFilters()->enable('context');
            $filter->setParameter('site', $this->contextService->getContext()->getSite()->getId());
        } catch (DomainException $e) {
        }
    }
}
