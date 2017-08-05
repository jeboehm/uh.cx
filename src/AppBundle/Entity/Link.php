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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *     name="link",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="site_name_idx",
 *              columns={"name", "site_id"}
 *          )
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinkRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Link implements ContextAwareInterface
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, options={"collation": "utf8_bin"})
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=5000, options={"collation": "utf8_bin"})
     */
    private $url = '';

    /**
     * @var string
     *
     * @ORM\Column(name="added_by", type="string", length=39)
     */
    private $addedBy = '';

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="links")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /**
     * @var Collection<Hit>
     *
     * @ORM\OneToMany(targetEntity="Hit", mappedBy="link", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $hits;

    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->hits = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Link
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Link
    {
        $this->url = $url;

        return $this;
    }

    public function getAddedBy(): string
    {
        return $this->addedBy;
    }

    public function setAddedBy(string $addedBy): Link
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getHits(): Collection
    {
        return $this->hits;
    }
}
