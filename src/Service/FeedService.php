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

namespace App\Service;

use App\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class FeedService
{
    private $repository;

    private $contextService;

    public function __construct(EntityManagerInterface $entityManager, ContextService $contextService)
    {
        $this->repository = $entityManager->getRepository(Link::class);
        $this->contextService = $contextService;
    }

    public function getFeed(): Feed
    {
        $feed = new Feed();
        $channel = $this->getChannel();
        $this->getItems($channel);
        $channel->appendTo($feed);

        return $feed;
    }

    private function getChannel(): Channel
    {
        $channel = new Channel();
        $channel
            ->title($this->contextService->getContext()->getSite()->getName())
            ->url($this->contextService->getContext()->getSite()->getMainUrl());

        return $channel;
    }

    private function getItems(Channel $channel): void
    {
        $links = $this->repository->getFeedItems();

        foreach ($links as $link) {
            $item = new Item();
            $item
                ->title(
                    sprintf('%s%s', $this->contextService->getContext()->getSite()->getMainUrl(), $link->getName())
                )
                ->url($link->getUrl())
                ->pubDate($link->getCreatedAt()->getTimestamp())
                ->contentEncoded($link->getUrl())
                ->appendTo($channel);
        }
    }
}
