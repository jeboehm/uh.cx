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

namespace AppBundle\Form\Data;

use AppBundle\Validator\Constraint\UrlBlacklist;
use Symfony\Component\Validator\Constraints as Assert;

class LinkData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Url()
     * @UrlBlacklist()
     */
    private $url = '';

    /**
     * @Assert\Type(type="bool")
     */
    private $reuse = false;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): LinkData
    {
        $this->url = $url;

        return $this;
    }

    public function isReuse(): bool
    {
        return $this->reuse;
    }

    public function setReuse(bool $reuse): LinkData
    {
        $this->reuse = $reuse;

        return $this;
    }
}
