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

use App\Entity\Link;

class UrlService
{
    private $contextService;

    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    public function getPreviewUrl(Link $link): string
    {
        return $this->contextService->getContext()->getSite()->getPreviewUrl() . $link->getName();
    }

    public function getShortUrl(Link $link): string
    {
        return $this->contextService->getContext()->getSite()->getMainUrl() . $link->getName();
    }

    public function getQrCodeImageUrl(string $url, int $width = 300, int $height = 300): string
    {
        $qr = 'https://chart.apis.google.com/chart?cht=qr&chs=%sx%s&chl=%s';

        return sprintf(
            $qr,
            $width,
            $height,
            $url
        );
    }
}
