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

use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../app/autoload.php';
Debug::enable();

Request::setTrustedProxies(['172.16.0.0/12']);
$request = Request::createFromGlobals();

$environment = 'dev';
if (strpos($request->getPathInfo(), '/admin') === 0) {
    $environment = 'dev_admin';
}

$kernel = new AppKernel($environment, true);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
