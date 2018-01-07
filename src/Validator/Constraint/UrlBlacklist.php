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

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UrlBlacklist extends Constraint
{
    public $message = 'The url {{ url }} is forbidden.';

    public function validatedBy(): string
    {
        return 'url_blacklist';
    }
}
