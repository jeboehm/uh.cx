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

namespace AppBundle\Command;

use AppBundle\Entity\Site;
use AppBundle\Repository\SiteRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteListCommand extends Command
{
    private $siteRepository;

    public function __construct(string $name, SiteRepository $siteRepository)
    {
        parent::__construct($name);

        $this->siteRepository = $siteRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('List configured sites.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['Name', 'Default URL', 'Preview URL', 'Default Site?']);

        /** @var Site[] $sites */
        $sites = $this->siteRepository->findAll();

        foreach ($sites as $site) {
            $table->addRow(
                [
                    $site->getName(),
                    $site->getMainUrl(),
                    $site->getPreviewUrl(),
                    $site->isDefault() ? '✓' : '✗',
                ]
            );
        }

        $table->render();

        return 0;
    }
}
