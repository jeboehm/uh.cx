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

use AppBundle\Form\Data\LinkData;
use AppBundle\Form\Type\LinkType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_default_default")
     * @Cache(smaxage=3600)
     */
    public function defaultAction(Request $request): Response
    {
        if ($request->get('tb') !== null) {
            return $this->forward('AppBundle:Default:toolbarButton', [], ['tb' => $request->get('tb')]);
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

            if ($form->isValid()) {
                $link = $this->container->get('app.manager.link')->create(
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
        if ($request->get('tb') === null) {
            throw new NotFoundHttpException();
        }

        $data = new LinkData();
        $data
            ->setUrl($request->get('tb'))
            ->setReuse(false);

        $errors = $this->container->get('validator')->validate($data);

        if ($errors->count() === 0) {
            $link = $this->container->get('app.manager.link')->create(
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
