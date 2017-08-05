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

use AppBundle\Entity\Link;
use AppBundle\Form\Data\LinkData;
use AppBundle\Form\Type\ReusableLinkType;
use AppBundle\Struct\Api\ResponseStruct;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends Controller
{
    /**
     * @Route("/api/create", name="app_api_create")
     */
    public function createAction(Request $request): Response
    {
        if (!$request->isMethod('post')) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        $encoded = $request->getContent();
        $decoded = json_decode($encoded, true, 3);

        if (!is_array($decoded)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        $data = new LinkData();
        $form = $this->createForm(ReusableLinkType::class, $data);
        $form->submit($decoded);

        if ($form->isValid()) {
            $link = $this->container->get('app.manager.link')->create(
                $data->isReuse(),
                $data->getUrl()
            );

            return new JsonResponse($this->getApiResponseStruct($link));
        }

        return new Response('Please specify a valid link.', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/api/create/json", name="app_api_compat_create")
     */
    public function compatCreateAction(): Response
    {
        return $this->forward('AppBundle:Api:create');
    }

    private function getApiResponseStruct(Link $link): ResponseStruct
    {
        $urlService = $this->container->get('app.service.url');

        return new ResponseStruct(
            $urlService->getShortUrl($link),
            $urlService->getPreviewUrl($link),
            $link->getUrl(),
            $urlService->getQrCodeImageUrl($urlService->getShortUrl($link)),
            $urlService->getQrCodeImageUrl($urlService->getPreviewUrl($link))
        );
    }
}
