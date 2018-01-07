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

namespace App\Command;

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteListCommand extends Command
{
    private $siteRepository;

    public function __construct(string $name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);

        $this->siteRepository = $entityManager->getRepository(Site::class);
    }

    protected function configure(): void
    {
        $this
            ->setName('site:list')
            ->setDescription('List configured sites.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['Name', 'Default URL', 'Preview URL', 'Default Site?', 'Secure?']);

        /** @var Site[] $sites */
        $sites = $this->siteRepository->findAll();

        foreach ($sites as $site) {
            $table->addRow(
                [
                    $site->getName(),
                    $site->getMainUrl(),
                    $site->getPreviewUrl(),
                    $site->isDefault() ? '✓' : '✗',
                    $site->isSecure() ? '✓' : '✗',
                ]
            );
        }

        $table->render();

        return 0;
    }
}
