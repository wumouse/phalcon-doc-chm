#!/usr/bin/env php
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
    $loader = new Loader();

    $loader->registerNamespaces([
        'Wumouse' => __DIR__ . '/../src'
    ])->register();
    $di = new Di();

    $application = new Script($di);
    try {
        $application->run($directory);
    } catch (\Exception $e) {
        echo get_class($e) , ':' , $e->getMessage() , PHP_EOL;
    }
});
