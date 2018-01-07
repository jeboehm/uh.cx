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

use App\CacheKernel;
use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpCache\Store;

require __DIR__ . '/../vendor/autoload.php';

$debug = '0' !== getenv('SYMFONY_DEBUG');
$environment = 'prod';
$request = Request::createFromGlobals();
Request::setTrustedProxies(['172.16.0.0/12'], Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);

if ($debug) {
    $environment = 'dev';
    umask(0000);
    Debug::enable();
}

$kernel = new Kernel($environment, $debug);

if (!$debug) {
    $store = new Store($kernel->getCacheDir() . '/html');
    $kernel = new CacheKernel($kernel, $store);
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
