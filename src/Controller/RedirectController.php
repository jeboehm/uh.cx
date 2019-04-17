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
use App\Service\ContextService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    private $linkManager;

    private $hitManager;

    private $contextService;

    public function __construct(LinkManager $linkManager, HitManager $hitManager, ContextService $contextService)
    {
        $this->linkManager = $linkManager;
        $this->hitManager = $hitManager;
        $this->contextService = $contextService;
    }

    /**
     * @Route("/{name}", name="app_redirect_default", requirements={"name": "[\w\d]+"})
     */
    public function defaultAction(string $name): Response
    {
        try {
            $link = $this->linkManager->get($name);
        } catch (LinkNotFoundException $e) {
            throw new NotFoundHttpException();
        }

        if ($this->contextService->getContext()->isPreview()) {
            return $this->redirectToRoute('app_info_preview', ['name' => $link->getName()]);
        }

        $this->hitManager->addHit($link, Hit::TYPE_REDIRECT);

        return $this->redirect($link->getUrl(), Response::HTTP_MOVED_PERMANENTLY);
    }
}
