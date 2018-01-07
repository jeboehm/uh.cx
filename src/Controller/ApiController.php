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

use App\Entity\Link;
use App\Form\Data\LinkData;
use App\Form\Type\ReusableLinkType;
use App\Manager\LinkManager;
use App\Service\UrlService;
use App\Struct\Api\ResponseStruct;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends Controller
{
    private $linkManager;

    private $urlService;

    public function __construct(LinkManager $linkManager, UrlService $urlService)
    {
        $this->linkManager = $linkManager;
        $this->urlService = $urlService;
    }

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
            $link = $this->linkManager->create(
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
        return $this->forward(__CLASS__ . '::createAction');
    }

    private function getApiResponseStruct(Link $link): ResponseStruct
    {
        return new ResponseStruct(
            $this->urlService->getShortUrl($link),
            $this->urlService->getPreviewUrl($link),
            $link->getUrl(),
            $this->urlService->getQrCodeImageUrl($this->urlService->getShortUrl($link)),
            $this->urlService->getQrCodeImageUrl($this->urlService->getPreviewUrl($link))
        );
    }
}
