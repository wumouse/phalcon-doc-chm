<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */


namespace Test\Other;

use Masterminds\HTML5;
use Phalcon\Di;

/**
 * @package Test\Other
 */
class Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @return \DOMDocument
     */
    public function setUp()
    {
        /** @var HTML5 $html5 */
        $html5 = Di::getDefault()->get('html5');
        $html = <<<'HTML'
<div align="center">
    <iframe src="//player.vimeo.com/video/68893840" width="500" height="313" frameborder="0" webkitAllowFullScreen
            mozallowfullscreen allowFullScreen></iframe>
</div>
<div align="center">
    <iframe src="//player.vimeo.com/video/69867342" width="500" height="313" frameborder="0" webkitAllowFullScreen
            mozallowfullscreen allowFullScreen></iframe>
</div>
HTML;
        $dom = $html5->loadHTML($html);
        $this->dom = $dom;
    }

    public function testDomNodeList()
    {
        /** @var \DOMElement $domElement */
        $divList = $this->dom->getElementsByTagName('iframe');
        foreach ($divList as $domElement) {
            $domElement->parentNode->removeChild($domElement);
        }
        $this->assertEquals($divList->length, 0);
    }
}
