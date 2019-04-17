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

namespace App\Controller;

use App\Service\FeedService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    private $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * @Route("/feed", name="app_feed_default")
     * @Cache(smaxage=600)
     */
    public function defaultAction(): Response
    {
        $feed = $this->feedService->getFeed();

        return new Response(
            $feed->render(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/atom+xml; charset=utf-8']
        );
    }
}
