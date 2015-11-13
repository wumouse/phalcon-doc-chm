<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */


namespace Test\Handler;

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
     * @var Main
     */
    protected $main;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $dependencyInjector = Di::getDefault();
        $this->main = new Main();
        $this->html5 = $dependencyInjector->get('html5');

        $this->dependencyInjector = $dependencyInjector;
    }

    public function testReplaceIFrameToAnchor()
    {
        $testHtml = __DIR__ . '/../data/iframe.html';
        $fixedHtml = __DIR__ . '/../data/iframe_fixed.html';
        
        $dom = $this->html5->loadHTMLFile($testHtml);
        $splFileInfo = new \SplFileInfo($testHtml);

        $this->main->localStyleSheetLink($dom);
        $this->main->replaceIFrameToAnchor($dom, $splFileInfo);

        $this->assertEquals(file_get_contents($fixedHtml), $this->html5->saveHTML($dom));
    }

    public function testRemoveJs()
    {
        $html = <<<'HTML'
<body>
    <script type="text/javascript">
        var a = 10;
    </script>
</body>
HTML;

        $dom = $this->html5->loadHTML($html);
        $this->main->removeJs($dom);
        $this->assertEquals(0, $dom->getElementsByTagName('script')->length);
    }
}
