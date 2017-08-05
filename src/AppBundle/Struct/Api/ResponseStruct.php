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

namespace AppBundle\Struct\Api;

use JsonSerializable;

class ResponseStruct implements JsonSerializable
{
    private $urlDirect;

    private $urlPreview;

    private $urlOriginal;

    private $qrDirect;

    private $qrPreview;

    public function __construct(
        string $urlDirect,
        string $urlPreview,
        string $urlOriginal,
        string $qrDirect,
        string $qrPreview
    ) {
        $this->urlDirect = $urlDirect;
        $this->urlPreview = $urlPreview;
        $this->urlOriginal = $urlOriginal;
        $this->qrDirect = $qrDirect;
        $this->qrPreview = $qrPreview;
    }

    public function jsonSerialize(): array
    {
        return [
            'UrlDirect' => $this->urlDirect,
            'UrlPreview' => $this->urlPreview,
            'UrlOriginal' => $this->urlOriginal,
            'QrDirect' => $this->qrDirect,
            'QrPreview' => $this->qrPreview,
        ];
    }
}
