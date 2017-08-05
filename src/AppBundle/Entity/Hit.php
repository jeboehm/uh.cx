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

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="hit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HitRepository")
 */
class Hit
{
    use TimestampableEntity;
    public const TYPE_PREVIEW = 1;
    public const TYPE_INFO = 2;
    public const TYPE_REDIRECT = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Link
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Link", inversedBy="hits")
     * @ORM\JoinColumn(name="link_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=1000)
     */
    private $userAgent = '';

    /**
     * @var string
     *
     * @ORM\Column(name="referer", type="string", length=1000)
     */
    private $referer = '';

    /**
     * @var string
     *
     * @ORM\Column(name="visited_by", type="string", length=39)
     */
    private $visitedBy = '';

    /**
     * @var int
     *
     * @ORM\Column(name="hit_type", type="smallint")
     */
    private $type = 0;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLink(): Link
    {
        return $this->link;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): Hit
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function setReferer(string $referer): Hit
    {
        $this->referer = $referer;

        return $this;
    }

    public function getVisitedBy(): string
    {
        return $this->visitedBy;
    }

    public function setVisitedBy(string $visitedBy): Hit
    {
        $this->visitedBy = $visitedBy;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): Hit
    {
        $this->type = $type;

        return $this;
    }
}
