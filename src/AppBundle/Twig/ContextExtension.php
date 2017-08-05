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

namespace AppBundle\Twig;

use AppBundle\Service\ContextService;
use DateTime;
use Twig_Extension;
use Twig_SimpleFunction;

class ContextExtension extends Twig_Extension
{
    private $contextService;

    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('context_site_title', [$this, 'getSiteTitle']),
            new Twig_SimpleFunction('context_test_mode', [$this, 'isTest']),
            new Twig_SimpleFunction('context_site_creation_date', [$this, 'getSiteCreationDate']),
            new Twig_SimpleFunction('context_site_main_url', [$this, 'getSiteMainUrl']),
        ];
    }

    public function getSiteTitle(): string
    {
        return $this->contextService->getContext()->getSite()->getName();
    }

    public function isTest(): bool
    {
        return $this->contextService->getContext()->getSite()->isTest();
    }

    public function getSiteCreationDate(): DateTime
    {
        return $this->contextService->getContext()->getSite()->getCreatedAt();
    }

    public function getSiteMainUrl(): string
    {
        return $this->contextService->getContext()->getSite()->getMainUrl();
    }
}
