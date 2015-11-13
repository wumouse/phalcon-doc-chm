#!/usr/local/bin/php
<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

use Phalcon\Di;
use Phalcon\Loader;
use Wumouse\Script;

call_user_func(function () {
    $directory = __DIR__ . '/../../phalcon_docs/zh/_build/htmlhelp';
    $loader = require __DIR__ . '/../vendor/autoload.php';

    $loader->setPsr4('Wumouse\\', __DIR__ . '/../src');
    $di = new Di();

    $application = new Script($di);
    $application->run($directory);
});
