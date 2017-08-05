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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SiteCreateCommand extends Command
{
    private $entityManager;

    private $validator;

    public function __construct(string $name, EntityManager $entityManager, ValidatorInterface $validator)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a site.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of your new site.')
            ->addArgument('host', InputArgument::REQUIRED, 'The host of your site. E.g. uh.cx')
            ->addArgument('previewHost', InputArgument::REQUIRED, 'The preview-host of your site. E.g. preview.uh.cx')
            ->addOption('secure', null, InputOption::VALUE_NONE, 'Is your site reachable via https?')
            ->addOption('default', null, InputOption::VALUE_NONE, 'Set the site as default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $site = new Site();

        foreach (array_merge($input->getArguments(), $input->getOptions()) as $key => $value) {
            try {
                $accessor->setValue($site, $key, $value);
            } catch (NoSuchPropertyException $e) {
            }
        }

        $violations = $this->validator->validate($site);

        foreach ($violations as $violation) {
            /* @var $violation ConstraintViolation */
            $output->writeln(sprintf('%s: %s', $violation->getPropertyPath(), $violation->getMessage()));
        }

        if (count($violations)) {
            return 1;
        }

        if ($site->isDefault()) {
            $repository = $this->entityManager->getRepository(Site::class);
            $repository->unsetDefault();
        }

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return 0;
    }
}
