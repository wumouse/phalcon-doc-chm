<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Test\Wumouse;

use Phalcon\Di;
use Wumouse\Script;

/**
 * @package Wumouse
 */
class ScriptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Script
     */
    protected $script;

    public function setUp()
    {
        $dependencyInjector = new Di();
        $dependencyInjector->setShared('eventsManager', 'Phalcon\Events\Manager');
        $this->script = new Script($dependencyInjector);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHandleException()
    {
        $invalidDirectory = '/abc/abc/abc';
        $this->script->run($invalidDirectory);
    }
}
