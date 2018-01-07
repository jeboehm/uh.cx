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

class LinkRepository extends EntityRepository
{
    /**
     * @return Link[]
     */
    public function getFeedItems(): array
    {
        return $this->findBy(
            [],
            ['createdAt' => 'desc'],
            20
        );
    }
}
