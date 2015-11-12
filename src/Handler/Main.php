<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse\Handler;

use Masterminds\HTML5;
use Phalcon\Di;
use Phalcon\Events\Event;
use Wumouse\File;
use Wumouse\Script;

/**
 * @package Wumouse\Handler
 */
class Main extends AbstractHandler
{
    /**
     * @var HTML5
     */
    protected $html5;

    /**
     */
    public function __construct()
    {
        $this->html5 = Di::getDefault()->get('html5');
    }

    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $splFileInfo = $file->getSplFileInfo();
        if ($splFileInfo->getExtension() != 'html') {
            return;
        }

        $dom = $this->html5->loadHTML($file->getContent());
        $this->localStyleSheetLink($dom);
        $this->replaceIFrameToAnchor($dom, $splFileInfo);

        $file->setContent($dom->saveHTML());
    }

    /**
     * @param \DOMDocument $dom
     */
    public function localStyleSheetLink(\DOMDocument $dom)
    {
        $links = $dom->getElementsByTagName('link');

        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $type = $link->attributes->getNamedItem('type');
            if (!$type || $type->nodeValue != 'text/css') {
                continue;
            }
            $href = $link->attributes->getNamedItem('href');
            $urlParts = parse_url($href->nodeValue);
            if (isset($urlParts['host'])) {
                if ($urlParts['host'] == 'fonts.googleapis.com') {
                    $link->parentNode->removeChild($link);
                } else {
                    $fileName = basename($urlParts['path']);
                    $href->nodeValue = '../_static/' . $fileName;
                }
            }
        }
    }

    /**
     * @param \DOMDocument $dom
     * @param \SplFileInfo $splFileInfo
     */
    public function replaceIFrameToAnchor(\DOMDocument $dom, \SplFileInfo $splFileInfo)
    {
        $pathName = $splFileInfo->getPathname();
        if (false !== strpos($pathName, '/api/')) {
            return;
        }
        $iFrames = $dom->getElementsByTagName('iframe');

        /** @var \DOMElement $iFrame */
        foreach ($iFrames as $iFrame) {
            $src = $iFrame->attributes->getNamedItem('src')->nodeValue;
            $anchor = $dom->createElement('a', $src);
            $anchor->setAttribute('href', $src);
            $iFrame->parentNode->replaceChild($anchor, $iFrame);
        }
    }
}
