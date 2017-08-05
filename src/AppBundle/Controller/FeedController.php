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

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller
{
    /**
     * @Route("/feed", name="app_feed_default")
     * @Cache(smaxage=600)
     */
    public function defaultAction(): Response
    {
        $feed = $this->container->get('app.service.feed')->getFeed();

        return new Response(
            $feed->render(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/atom+xml; charset=utf-8']
        );
    }
}
