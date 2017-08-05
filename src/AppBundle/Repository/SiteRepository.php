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

namespace AppBundle\Repository;

use AppBundle\Entity\Site;
use AppBundle\Exception\SiteNotFoundException;
use Doctrine\ORM\EntityRepository;
use DomainException;

class SiteRepository extends EntityRepository
{
    public function findOneByHost(string $host): Site
    {
        $host = mb_strtolower($host);

        $qb = $this->createQueryBuilder('site');
        $qb
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('site.host', ':host'),
                    $qb->expr()->eq('site.previewHost', ':host'),
                    $qb->expr()->eq('site.default', 1)
                )
            )
            ->setMaxResults(1)
            ->setParameter('host', $host);

        $site = $qb->getQuery()->getOneOrNullResult();

        if (!($site instanceof Site)) {
            throw new SiteNotFoundException(sprintf('Site "%s" was not found.', $host));
        }

        return $site;
    }

    public function getDefault(): Site
    {
        /** @var Site $site */
        $site = $this->findOneBy(['default' => 1]);

        if ($site === null) {
            throw new DomainException('No default site was found.');
        }

        return $site;
    }

    public function unsetDefault(): void
    {
        /** @var Site[] $sites */
        $sites = $this->findAll();

        foreach ($sites as $site) {
            $site->setDefault(false);
        }

        $this->getEntityManager()->flush();
    }

    public function getAllUrls(): array
    {
        $urls = [];
        $sites = $this->findAll();

        /** @var Site $site */
        foreach ($sites as $site) {
            $urls[] = $site->getHost();
            $urls[] = $site->getPreviewHost();
        }

        return array_unique($urls);
    }
}
