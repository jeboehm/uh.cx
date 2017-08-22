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

namespace Tests\Functional\Command;

use AppBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Functional\AbstractFunctionalTestCase;
use Tests\Functional\HelperTrait;

class SiteCreateCommandTest extends AbstractFunctionalTestCase
{
    use HelperTrait;

    public function testCreateSiteValidatesUserInput(): void
    {
        $client = static::createDefaultClient();
        $commandTester = $this->createCommandTester($client);
        $commandTester->execute(
            [
                'command'     => 'site:create',
                'name'        => 'example',
                'host'        => static::HOST_DEFAULT,
                'previewHost' => 'preview.example.com',
                '--secure'    => true,
                '--default'   => true,
            ]
        );

        $this->assertContains('host', $commandTester->getDisplay());
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    private function createCommandTester(Client $client): CommandTester
    {
        $application = new Application($client->getKernel());
        $application->add($client->getContainer()->get('app.command.site_create'));

        $command = $application->find('site:create');

        return new CommandTester($command);
    }

    public function testCreateSite(): void
    {
        $client = static::createDefaultClient();
        $commandTester = $this->createCommandTester($client);

        $siteName = uniqid('site', true);
        $siteHost = 'example.com';
        $sitePreviewHost = 'preview.example.com';

        $commandTester->execute(
            [
                'command'     => 'site:create',
                'name'        => $siteName,
                'host'        => $siteHost,
                'previewHost' => $sitePreviewHost,
                '--secure'    => true,
                '--default'   => true,
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());

        $repository = $this->getEntityManager($client)->getRepository(Site::class);
        $site = $repository->getDefault();

        $this->assertEquals($siteName, $site->getName());
        $this->assertEquals($siteHost, $site->getHost());
        $this->assertEquals($sitePreviewHost, $site->getPreviewHost());
    }
}
