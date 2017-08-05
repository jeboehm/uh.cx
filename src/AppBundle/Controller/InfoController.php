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

use AppBundle\Entity\Hit;
use AppBundle\Exception\LinkNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InfoController extends Controller
{
    /**
     * @Route("/info/{name}", name="app_info_info", requirements={"name": "[\w\d]+"})
     */
    public function infoAction(string $name): Response
    {
        try {
            $link = $this->container->get('app.manager.link')->get($name);
        } catch (LinkNotFoundException $e) {
            throw new NotFoundHttpException();
        }

        $this->container->get('app.manager.hit')->addHit($link, Hit::TYPE_INFO);

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
            $link = $this->container->get('app.manager.link')->get($name);
        } catch (LinkNotFoundException $e) {
            throw new NotFoundHttpException();
        }

        $this->container->get('app.manager.hit')->addHit($link, Hit::TYPE_PREVIEW);

        return $this->render(
            'info/preview.html.twig',
            [
                'link' => $link,
            ]
        );
    }
}
