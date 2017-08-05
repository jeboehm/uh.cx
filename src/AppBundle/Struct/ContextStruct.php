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

namespace AppBundle\Struct;

use AppBundle\Entity\Site;

class ContextStruct
{
    private $site;

    private $preview;

    private $clientIp;

    private $referer;

    private $userAgent;

    public function __construct(Site $site, bool $preview, string $clientIp, string $referer, string $userAgent)
    {
        $this->site = $site;
        $this->preview = $preview;
        $this->clientIp = $clientIp;
        $this->referer = $referer;
        $this->userAgent = $userAgent;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function isPreview(): bool
    {
        return $this->preview;
    }

    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
