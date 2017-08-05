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

namespace AppBundle\Validator\Constraint;

use AppBundle\Repository\SiteRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UrlBlacklistValidator extends ConstraintValidator
{
    private $siteRepository;

    public function __construct(SiteRepository $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        $urls = $this->siteRepository->getAllUrls();

        foreach ($urls as $url) {
            if (stripos($value, 'http://' . $url) === 0 || stripos($value, 'https://' . $url) === 0) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ url }}', $value)
                    ->addViolation();
            }
        }
    }
}
