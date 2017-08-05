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

namespace Tests\Functional\Subscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\Functional\AbstractFunctionalTestCase;
use Tests\Functional\HelperTrait;

class PreviewPageRedirectSubscriberTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testPreviewHostRequestWillBeRedirected(): void
    {
        $client = static::createPreviewClient();
        $link = $this->createLink($client);

        $client->request('GET', $this->generateUrl($client, 'app_info_info', ['name' => $link->getName()]));
        $response = $client->getResponse();

        if (!($response instanceof RedirectResponse)) {
            $this->fail('Expected redirect');
        } else {
            $this->assertEquals($link->getSite()->getMainUrl() . 'info/' . $link->getName(), $response->getTargetUrl());
        }
    }

    public function testNoLinkRedirectOnPreviewHost(): void
    {
        $client = static::createPreviewClient();
        $link = $this->createLink($client);

        $client->request('GET', '/' . $link->getName());
        $response = $client->getResponse();

        if (!($response instanceof RedirectResponse)) {
            $this->fail('Expected redirect');
        } else {
            $this->assertEquals('/preview/' . $link->getName(), $response->getTargetUrl());
        }
    }
}
