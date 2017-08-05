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

namespace AppBundle\Subscriber;

use AppBundle\Controller\RedirectController;
use AppBundle\Service\ContextService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PreviewPageRedirectSubscriber implements EventSubscriberInterface
{
    private $contextService;

    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }

    public function onController(FilterControllerEvent $event): void
    {
        if (!$this->contextService->getContext()->isPreview()) {
            return;
        }

        if (!($event->getController()[0] instanceof RedirectController)) {
            $url = sprintf(
                '%s%s',
                rtrim($this->contextService->getContext()->getSite()->getMainUrl(), '/'),
                $event->getRequest()->getRequestUri()
            );

            $event->setController(
                function () use ($url) {
                    return new RedirectResponse($url, RedirectResponse::HTTP_MOVED_PERMANENTLY);
                }
            );
        }
    }
}
