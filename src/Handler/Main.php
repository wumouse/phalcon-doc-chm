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
        $this->removeJs($dom);

        $file->setContent($this->html5->saveHTML($dom));
    }

    /**
     * localize the stylesheet from other domain
     *
     * @param \DOMDocument $dom
     */
    public function localStyleSheetLink(\DOMDocument $dom)
    {
        $links = $dom->getElementsByTagName('link');

        $this->loopNodeListCallback($links, function ($link) {
            /** @var \DOMElement $link */

            $type = $link->attributes->getNamedItem('type');
            if (!$type || $type->nodeValue != 'text/css') {
                return;
            }
            $href = $link->attributes->getNamedItem('href');
            $urlParts = parse_url($href->nodeValue);
            if (isset($urlParts['host'])) {
                if ($urlParts['host'] == 'fonts.googleapis.com') {
                    if ($link->previousSibling instanceof \DOMText) {
                        $link->parentNode->removeChild($link->previousSibling);
                    }
                    $link->parentNode->removeChild($link);
                } else {
                    $fileName = basename($urlParts['path']);
                    $href->nodeValue = '../_static/' . $fileName;
                }
            }
        });
    }

    /**
     * replace tag iframe to a
     *
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

        $this->loopNodeListCallback($iFrames, function ($iFrame) use ($dom) {
            /** @var \DOMElement $iFrame */

            $src = $iFrame->attributes->getNamedItem('src')->nodeValue;
            $anchor = $dom->createElement('a', $src);
            $anchor->setAttribute('href', $src);
            $iFrame->parentNode->replaceChild($anchor, $iFrame);
        });
    }

    /**
     * remove the single one script tag
     *
     * @param \DOMDocument $dom
     */
    public function removeJs(\DOMDocument $dom)
    {
        $scripts = $dom->getElementsByTagName('script');

        $this->loopNodeListCallback($scripts, function ($script) {
            $script->parentNode->removeChild($script);
        });
    }

    /**
     * fetch node from DOMNodeList with callback
     *
     * because remove child reduce the internal item with index, so DOMNodeList::item($index) will get an error after
     * removed. so there use a reverse order to iterate keep that max index will be removed so DOMNodeList::item($index)
     * always get an non-removed node
     *
     * $this->loopNodeListCallback($nodeList, function ($currentNode) {
     *      if ($someCondition) {
     *          return true;// continue the loop;
     *      }
     *
     *      if ($someCondition) {
     *          return false;// break the loop;
     *      }
     * });
     *
     * @param \DOMNodeList $list
     * @param callable $callback
     */
    public function loopNodeListCallback(\DOMNodeList $list, callable $callback)
    {
        for ($index = ($list->length - 1); $index >= 0; $index--) {
            $return = call_user_func($callback, $list->item($index));

            if (false === $return) {
                break;
            }
            if (true === $return) {
                continue;
            }
        }
    }
}
