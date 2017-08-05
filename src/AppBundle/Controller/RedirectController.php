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

class RedirectController extends Controller
{
    /**
     * @Route("/{name}", name="app_redirect_default", requirements={"name": "[\w\d]+"})
     */
    public function defaultAction(string $name): Response
    {
        try {
            $link = $this->container->get('app.manager.link')->get($name);
        } catch (LinkNotFoundException $e) {
            throw new NotFoundHttpException();
        }

        if ($this->container->get('app.service.context')->getContext()->isPreview()) {
            return $this->redirectToRoute('app_info_preview', ['name' => $link->getName()]);
        }

        $this->container->get('app.manager.hit')->addHit($link, Hit::TYPE_REDIRECT);

        return $this->redirect($link->getUrl(), Response::HTTP_MOVED_PERMANENTLY);
    }
}
