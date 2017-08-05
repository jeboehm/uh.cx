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

namespace AppBundle\Admin;

use AppBundle\Entity\Link;
use AppBundle\Entity\Site;
use AppBundle\Repository\SiteRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class LinkAdmin extends AbstractAdmin
{
    /**
     * @var SiteRepository
     */
    private $siteRepository;

    public function setSiteRepository(SiteRepository $siteRepository): void
    {
        $this->siteRepository = $siteRepository;
    }

    public function toString($object): string
    {
        return $object instanceof Link ? $object->getName() : 'Link';
    }

    protected function configureFormFields(FormMapper $form): void
    {
        parent::configureFormFields($form);

        $form
            ->add(
                'name',
                'text',
                [
                    'disabled' => true,
                ]
            )
            ->add(
                'url',
                'text',
                [
                    'disabled' => true,
                ]
            )
            ->add(
                'addedBy',
                'text',
                [
                    'disabled' => true,
                ]
            )
            ->add(
                'site',
                'entity',
                [
                    'class' => Site::class,
                    'choice_label' => 'name',
                    'disabled' => true,
                ]
            );
    }

    protected function configureListFields(ListMapper $list): void
    {
        parent::configureListFields($list);

        $list
            ->addIdentifier('name')
            ->add('url')
            ->add('addedBy')
            ->add('site.name')
            ->add('createdAt')
            ->add('updatedAt');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);

        $filter->add('site.name');
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        parent::configureRoutes($collection);

        $collection->remove('create');
    }
}
