<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse\Handler;

use Phalcon\Di;
use Phalcon\Events\Event;
use Phalcon\Mvc\ViewInterface;
use Wumouse\File;
use Wumouse\Index;
use Wumouse\Script;

/**
 * @package Wumouse\Handler
 */
class Main extends AbstractHandler
{
    /**
     * @var Index
     */
    protected $index;

    /**
     * Need download to local
     *
     * @var array
     */
    protected $localStatic = [];

    /**
     */
    public function __construct()
    {
        $this->index = new Index();
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

        $html = $this->normalizeHtml5ToHtml4($file->getContent());

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $this->localStyleSheetLink($dom);
        $this->replaceIFrameToAnchor($dom, $splFileInfo);
        $this->removeJs($dom);
        $this->removeFooter($dom);
        $this->changeEncodingMeta($dom);

        if (basename($splFileInfo->getPath()) === 'api') {
            try {
                $this->index->handle($dom, $splFileInfo);
            } catch (\RuntimeException $e) {
                echo $e->getMessage() , PHP_EOL;
            }
        }

        $file->setContent($dom->saveHTML());
    }

    /**
     * @inheritDoc
     */
    public function afterIterate(Event $event, Script $script)
    {
        $this->renderIndex($script);
        $this->downloadStatic($script);
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
                    if (!array_key_exists($fileName, $this->localStatic)) {
                        $url = $href->nodeValue;
                        if (!isset($urlParts['scheme'])) {
                            $url = 'http:' . $url;
                        }
                        $this->localStatic[$fileName] = $url;
                    }
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
        if (basename($splFileInfo->getPath()) != 'api') {
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

    /**
     * 去掉 footer
     *
     * @param \DOMDocument $dom
     */
    public function removeFooter(\DOMDocument $dom)
    {
        $footer = $dom->getElementById('footer');
        if ($footer) {
            $footer->parentNode->removeChild($footer);
        }
    }

    /**
     * @param \DOMDocument $dom
     */
    public function changeEncodingMeta(\DOMDocument $dom)
    {
        $metaNodes = $dom->getElementsByTagName('meta');
        if ($metaNodes->length) {
            foreach ($metaNodes as $metaNode) {
                /** @var \DOMElement $metaNode */
                $httpEquiv = $metaNode->attributes->getNamedItem('http-equiv');
                if ($httpEquiv && $httpEquiv->nodeValue == 'Content-Type') {
                    $content = $metaNode->attributes->getNamedItem('content');
                    if ($content) {
                        $content->nodeValue = 'text/html; charset=gb2312';
                    }
                }
            }
        }
    }

    /**
     * @param string $html
     * @return string
     */
    public function normalizeHtml5ToHtml4($html)
    {
        return str_replace(
            ['<header', '</header>', '<footer', '</footer>', '<nav', '</nav>'],
            ['<div id="header"', '</div>', '<div id="footer"', '</div>', '<div id="nav"', '</div>'],
            $html
        );
    }

    /**
     * @param Script $script
     */
    public function renderIndex(Script $script)
    {
        /** @var ViewInterface $view */
        $view = $script->getDependencyInjector()->getShared('view');

        ob_start();
        $view->partial('indexTpl', ['index' => $this->index]);

        file_put_contents($script->getDirectory() . '/PhalconDocumentationdoc.hhk', ob_get_clean());
    }

    /**
     * @param Script $script
     */
    public function downloadStatic(Script $script)
    {
        $curl = curl_init();
        foreach ($this->localStatic as $fileName => $link) {
            curl_reset($curl);
            curl_setopt_array($curl, [
                CURLOPT_URL => $link,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_TIMEOUT => 3,
            ]);

            $content = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode != 200) {
                echo "downloading static file: {$link} failed, response {$httpCode}" , PHP_EOL;
                continue;
            }
            echo "downloaded static file: {$link}" , PHP_EOL;
            file_put_contents($script->getDirectory() . '/_static/' . $fileName, $content);
        }
    }
}
