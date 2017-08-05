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

use AppBundle\Entity\Site;
use AppBundle\Repository\SiteRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SiteAdmin extends AbstractAdmin
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
        return $object instanceof Site ? $object->getName() : 'Site';
    }

    public function prePersist($object): void
    {
        parent::prePersist($object);

        if ($object instanceof Site) {
            if ($object->isDefault()) {
                $this->siteRepository->unsetDefault();
                $object->setDefault(true);
            }
        }
    }

    public function preUpdate($object)
    {
        parent::preUpdate($object);

        if ($object instanceof Site) {
            if ($object->isDefault()) {
                $this->siteRepository->unsetDefault();
                $object->setDefault(true);
            }
        }
    }

    protected function configureFormFields(FormMapper $form): void
    {
        parent::configureFormFields($form);

        $form
            ->add('name', 'text')
            ->add('host', 'text')
            ->add('previewHost', 'text')
            ->add('secure', 'checkbox', ['required' => false])
            ->add('test', 'checkbox', ['required' => false])
            ->add('default', 'checkbox', ['required' => false]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        parent::configureListFields($list);

        $list
            ->addIdentifier('name')
            ->add('host')
            ->add('secure')
            ->add('default')
            ->add('createdAt')
            ->add('updatedAt');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        parent::configureDatagridFilters($filter);

        $filter->add('name');
    }
}
