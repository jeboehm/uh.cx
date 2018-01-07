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

namespace App\Twig;

use App\Entity\Link;
use App\Service\UrlService;
use Twig_Extension;
use Twig_SimpleFilter;

class LinkExtension extends Twig_Extension
{
    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('link_preview_url', [$this, 'getPreviewUrl']),
            new Twig_SimpleFilter('link_short_url', [$this, 'getShortUrl']),
            new Twig_SimpleFilter('link_qr', [$this, 'getQrCodeImageUrl']),
        ];
    }

    public function getPreviewUrl(Link $link): string
    {
        return $this->urlService->getPreviewUrl($link);
    }

    public function getShortUrl(Link $link): string
    {
        return $this->urlService->getShortUrl($link);
    }

    public function getQrCodeImageUrl(string $url, int $width = 300, int $height = 300): string
    {
        return $this->urlService->getQrCodeImageUrl($url, $width, $height);
    }
}
