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
use App\Tests\Functional\HelperTrait;
use Symfony\Component\HttpFoundation\Response;

class SiteAdminTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testAdminListRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->generateUrl($client, 'admin_app_site_list'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testAdminCreateRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', $this->generateUrl($client, 'admin_app_site_create'));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
