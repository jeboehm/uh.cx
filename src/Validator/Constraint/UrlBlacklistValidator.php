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

namespace App\Validator\Constraint;

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UrlBlacklistValidator extends ConstraintValidator
{
    private $siteRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->siteRepository = $entityManager->getRepository(Site::class);
    }

    public function validate($value, Constraint $constraint): void
    {
        $urls = $this->siteRepository->getAllUrls();

        foreach ($urls as $url) {
            if (0 === stripos($value, 'http://' . $url) || 0 === stripos($value, 'https://' . $url)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ url }}', $value)
                    ->addViolation();
            }
        }
    }
}
