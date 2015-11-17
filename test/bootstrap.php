<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */
use Composer\Autoload\ClassLoader;
use Phalcon\Di;
use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces([
    'Wumouse' => __DIR__ . '/../src',
    'Test' => __DIR__ . '/../test',
])->register();

$di = new Di();

//$di->setShared('eventsManager', 'Phalcon\Events\Manager');
//$di->setShared('html5', 'Masterminds\HTML5');
