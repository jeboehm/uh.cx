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

namespace App\Tests\Functional\Admin;

use App\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class LinkAdminTest extends AbstractFunctionalTestCase
{
    public function testAdminListRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', $client->getContainer()->get('router')->generate('admin_app_link_list'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
