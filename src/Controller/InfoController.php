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

use App\Entity\Hit;
use App\Exception\LinkNotFoundException;
use App\Manager\HitManager;
use App\Manager\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    private $linkManager;

    private $hitManager;

    public function __construct(LinkManager $linkManager, HitManager $hitManager)
    {
        $this->linkManager = $linkManager;
        $this->hitManager = $hitManager;
    }

    /**
     * @Route("/info/{name}", name="app_info_info", requirements={"name": "[\w\d]+"})
     */
    public function infoAction(string $name): Response
    {
        try {
            $link = $this->linkManager->get($name);
        } catch (LinkNotFoundException $e) {
            throw new NotFoundHttpException();
        }

        $this->hitManager->addHit($link, Hit::TYPE_INFO);

        return $this->render(
            'info/info.html.twig',
            [
                'link' => $link,
            ]
        );
    }

    /**
     * @Route("/preview/{name}", name="app_info_preview", requirements={"name": "[\w\d]+"})
     */
    public function previewAction(string $name): Response
    {
        try {
            $link = $this->linkManager->get($name);
        } catch (LinkNotFoundException $e) {
            throw new NotFoundHttpException();
        }

        $this->hitManager->addHit($link, Hit::TYPE_PREVIEW);

        return $this->render(
            'info/preview.html.twig',
            [
                'link' => $link,
            ]
        );
    }
}
