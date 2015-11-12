<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */


namespace Test\Handler;

use Masterminds\HTML5;
use Phalcon\Di;
use Phalcon\DiInterface;
use Wumouse\Handler\Main;

/**
 * @package Test\Handler
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DiInterface
     */
    protected $dependencyInjector;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->dependencyInjector = Di::getDefault();
    }

    public function testReplaceIFrameToAnchor()
    {
        $main = new Main();
        $testHtml = __DIR__ . '/../data/debug.html';
        /** @var HTML5 $html5 */
        $html5 = $this->dependencyInjector->get('html5');
        $dom = $html5->loadHTMLFile($testHtml);
        $content = $dom->saveHTML();
        $splFileInfo = new \SplFileInfo($testHtml);

        $main->localStyleSheetLink($dom);
        $main->replaceIFrameToAnchor($dom, $splFileInfo);

        $this->assertEquals($content, $dom->saveHTML());
    }
}
