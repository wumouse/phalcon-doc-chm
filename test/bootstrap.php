<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */
use Composer\Autoload\ClassLoader;
use Phalcon\Di;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->setPsr4('Wumouse\\', __DIR__ . '/../src');
$loader->setPsr4('Test\\', __DIR__);

$di = new Di();

$di->setShared('eventsManager', 'Phalcon\Events\Manager');
$di->setShared('html5', 'Masterminds\HTML5');
