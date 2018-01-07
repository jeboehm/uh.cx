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

namespace App\Repository;

use App\Entity\Link;
use Doctrine\ORM\EntityRepository;

class HitRepository extends EntityRepository
{
    public function getHitsGroupedByType(Link $link): array
    {
        $qb = $this->createQueryBuilder('hit');
        $qb
            ->select('hit.type')
            ->addSelect('COUNT(hit.id) AS hits')
            ->addGroupBy('hit.type')
            ->andWhere($qb->expr()->eq('hit.link', ':link'))
            ->addOrderBy('hit.type', 'ASC')
            ->setParameter('link', $link);

        $result = $qb->getQuery()->getResult();
        $return = [];

        foreach ($result as $row) {
            $return[$row['type']] = (int) $row['hits'];
        }

        return $return;
    }
}
