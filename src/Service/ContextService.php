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

namespace App\Service;

use App\Entity\Site;
use App\Struct\ContextStruct;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Component\HttpFoundation\RequestStack;

class ContextService
{
    private $requestStack;

    private $siteRepository;

    private $context;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->siteRepository = $entityManager->getRepository(Site::class);
    }

    public function getContext(): ContextStruct
    {
        $request = $this->requestStack->getMasterRequest();

        if (!$request) {
            throw new DomainException('No request found.');
        }

        if (null !== $this->context) {
            return $this->context;
        }

        $host = $request->getHost();
        $site = $this->siteRepository->findOneByHost($host);
        $preview = false;

        if ($site->getPreviewHost() === $host) {
            $preview = true;
        }

        $this->context = new ContextStruct(
            $site,
            $preview,
            $request->getClientIp(),
            $request->headers->get('referer', ''),
            $request->headers->get('User-Agent', '')
        );

        return $this->context;
    }
}
