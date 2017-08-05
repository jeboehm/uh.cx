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
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SiteRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("host")
 * @UniqueEntity("previewHost")
 */
class Site
{
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $host = '';

    /**
     * @var string
     *
     * @ORM\Column(name="preview_host", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $previewHost = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="secure", type="boolean")
     */
    private $secure = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="test", type="boolean")
     */
    private $test = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="default_site", type="boolean")
     */
    private $default = false;

    /**
     * @var Collection<Link>
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="site", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $links;

    public function __construct()
    {
        $this->links = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Site
    {
        $this->name = $name;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): Site
    {
        $this->host = $host;

        return $this;
    }

    public function getPreviewHost(): string
    {
        return $this->previewHost;
    }

    public function setPreviewHost(string $previewHost): Site
    {
        $this->previewHost = $previewHost;

        return $this;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function setSecure(bool $secure): Site
    {
        $this->secure = $secure;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): Site
    {
        $this->default = $default;

        return $this;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function setTest(bool $test): Site
    {
        $this->test = $test;

        return $this;
    }

    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function getMainUrl(): string
    {
        return sprintf(
            '%s://%s/',
            $this->secure ? 'https' : 'http',
            $this->host
        );
    }

    public function getPreviewUrl(): string
    {
        return sprintf(
            '%s://%s/',
            $this->secure ? 'https' : 'http',
            $this->previewHost
        );
    }
}
