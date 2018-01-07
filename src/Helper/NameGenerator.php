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

namespace App\Helper;

class NameGenerator
{
    public static function generate(int $length, array $chars = []): string
    {
        if (empty($chars)) {
            $chars = array_merge(
                range(0, 9),
                range('a', 'z'),
                range('A', 'Z')
            );
        }

        $name = null;
        $runs = 0;

        while (!$name) {
            for ($i = 1; $i <= count($chars) * 2; ++$i) {
                $swap = random_int(0, count($chars) - 1);
                $tmp = $chars[$swap];
                $chars[$swap] = $chars[0];
                $chars[0] = $tmp;
            }

            ++$runs;
            $name = substr(implode('', $chars), 0, $length);
        }

        return $name;
    }
}
