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

namespace App\Admin;

use App\Entity\Link;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LinkAdmin extends AbstractAdmin
{
    private $siteRepository;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->siteRepository = $entityManager->getRepository(Site::class);
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
                TextType::class,
                [
                    'disabled' => true,
                ]
            )
            ->add(
                'url',
                TextType::class,
                [
                    'disabled' => true,
                ]
            )
            ->add(
                'addedBy',
                TextType::class,
                [
                    'disabled' => true,
                ]
            )
            ->add(
                'site',
                EntityType::class,
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
