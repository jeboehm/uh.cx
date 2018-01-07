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

namespace App\Tests\Functional\Command;

use App\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SiteListCommandTest extends AbstractFunctionalTestCase
{
    public function testCreateSite(): void
    {
        $client = static::createDefaultClient();
        $commandTester = $this->createCommandTester($client);

        $commandTester->execute(
            [
                'command' => 'site:list',
            ]
        );

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertContains('Testenvironment', $commandTester->getDisplay());
        $this->assertContains(static::HOST_PREVIEW, $commandTester->getDisplay());
    }

    private function createCommandTester(Client $client): CommandTester
    {
        $application = new Application($client->getKernel());
        $command = $application->find('site:list');

        return new CommandTester($command);
    }
}
