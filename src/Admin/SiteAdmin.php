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

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SiteAdmin extends AbstractAdmin
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
            ->add('name', TextType::class)
            ->add('host', TextType::class)
            ->add('previewHost', TextType::class)
            ->add('secure', CheckboxType::class, ['required' => false])
            ->add('test', CheckboxType::class, ['required' => false])
            ->add('default', CheckboxType::class, ['required' => false]);
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
