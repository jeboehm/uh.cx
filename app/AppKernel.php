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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new AppBundle\AppBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        if (in_array($this->getEnvironment(), ['dev_admin', 'test_admin', 'prod_admin'], true)) {
            $bundles[] = new Symfony\Bundle\SecurityBundle\SecurityBundle();
            $bundles[] = new Sonata\CoreBundle\SonataCoreBundle();
            $bundles[] = new Sonata\BlockBundle\SonataBlockBundle();
            $bundles[] = new Knp\Bundle\MenuBundle\KnpMenuBundle();
            $bundles[] = new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle();
            $bundles[] = new Sonata\AdminBundle\SonataAdminBundle();
        }

        return $bundles;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/app/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/app/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    public function getRootDir(): string
    {
        return __DIR__;
    }
}
