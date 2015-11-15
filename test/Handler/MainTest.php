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
     * @var \DOMDocument
     */
    public $dom;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $dependencyInjector = Di::getDefault();
        $this->main = new Main();
        $this->dom = new \DOMDocument();
        $this->dom->formatOutput = true;

        $this->dependencyInjector = $dependencyInjector;
    }

    public function testReplaceIFrameToAnchor()
    {
        $testHtml = __DIR__ . '/../data/iframe.html';
        $fixedHtml = __DIR__ . '/../data/iframe_fixed.html';
        
        $this->dom->loadHTMLFile($testHtml);
        $splFileInfo = new \SplFileInfo('api/test.html');

        $this->main->localStyleSheetLink($this->dom, new \SplFileInfo($testHtml));
        $this->main->replaceIFrameToAnchor($this->dom, $splFileInfo);

        $this->assertEquals(file_get_contents($fixedHtml), $this->dom->saveHTML());
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

        $this->dom->loadHTML($html);
        $this->main->removeJs($this->dom);
        $this->assertEquals(0, $this->dom->getElementsByTagName('script')->length);
    }

    public function testRemoveComments()
    {
        $fileBaseName = __DIR__ . '/../data/comment';
        $content = file_get_contents($fileBaseName  . '.html');
        $replacedContent = $this->main->removeComments($content);
        $this->assertEquals(file_get_contents($fileBaseName . '_fixed.html'), $replacedContent);
    }
}
