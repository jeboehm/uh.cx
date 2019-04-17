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

use App\Form\Data\LinkData;
use App\Form\Type\LinkType;
use App\Manager\LinkManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultController extends AbstractController
{
    private $linkManager;

    private $validator;

    public function __construct(LinkManager $linkManager, ValidatorInterface $validator)
    {
        $this->linkManager = $linkManager;
        $this->validator = $validator;
    }

    /**
     * @Route("/", name="app_default_default")
     * @Cache(smaxage=86400)
     */
    public function defaultAction(Request $request): Response
    {
        if (null !== $request->get('tb')) {
            return $this->forward(__CLASS__ . '::toolbarButtonAction', [], ['tb' => $request->get('tb')]);
        }

        $hasError = false;
        $data = new LinkData();
        $form = $this->createForm(
            LinkType::class,
            $data,
            ['action' => $this->generateUrl('app_default_default')]
        );

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $link = $this->linkManager->create(
                    $data->isReuse(),
                    $data->getUrl()
                );

                return $this->redirectToRoute('app_info_info', ['name' => $link->getName()]);
            }

            $hasError = true;
        }

        return $this->render(
            'default/index.html.twig',
            [
                'form' => $form->createView(),
                'hasError' => $hasError,
            ]
        );
    }

    public function toolbarButtonAction(Request $request): Response
    {
        if (null === $request->get('tb')) {
            throw new NotFoundHttpException();
        }

        $data = new LinkData();
        $data
            ->setUrl($request->get('tb'))
            ->setReuse(false);

        $errors = $this->validator->validate($data);

        if (0 === $errors->count()) {
            $link = $this->linkManager->create(
                $data->isReuse(),
                $data->getUrl()
            );

            return $this->redirectToRoute('app_info_info', ['name' => $link->getName()]);
        }

        $form = $this->createForm(
            LinkType::class,
            $data,
            ['action' => $this->generateUrl('app_default_default')]
        );

        return $this->render(
            'default/index.html.twig',
            [
                'form' => $form->createView(),
                'hasError' => true,
            ]
        );
    }
}
