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
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\Manager;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\ViewInterface;
use Wumouse\File;
use Wumouse\Index;
use Wumouse\Script;

/**
 * @package Wumouse\Handler
 */
class Main extends AbstractHandler implements EventsAwareInterface
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
     * @var Manager
     */
    protected $eventsManager;

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

        $html = $this->normalizeHtml5ToHtml4($file->getContent());
        $html = $this->removeComments($html);

        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $dom->loadHTML($html);

        $this->eventsManager->fire('main:beforeChangeDom', $this, $dom, false);

        $this->replaceEncodingMeta($dom);
        $this->localStyleSheetLink($dom, $splFileInfo);
        $this->replaceIFrameToAnchor($dom, $splFileInfo);
        $this->removeJs($dom);
        $this->removeHeaderFooter($dom);

        if (basename($splFileInfo->getPath()) === 'api') {
            try {
                $this->index->handle($dom, $splFileInfo);
            } catch (\RuntimeException $e) {
                echo $e->getMessage() , PHP_EOL;
            }
        }

        $this->eventsManager->fire('main:afterChangeDom', $this, [$dom, $splFileInfo], false);

        $file->setContent($dom->saveHTML());
    }

    /**
     * @inheritDoc
     */
    public function afterIterate(Event $event, Script $script)
    {
        $this->renderIndex($script);
        $this->cleanNoNeededStatic($script);
        $this->downloadStatic($script);
    }

    /**
     * localize the stylesheet from other domain
     *
     * @param \DOMDocument $dom
     * @param \SplFileInfo|File $splFileInfo
     */
    public function localStyleSheetLink(\DOMDocument $dom, \SplFileInfo $splFileInfo)
    {
        $links = $dom->getElementsByTagName('link');

        $this->loopNodeListCallback($links, function (\DOMElement $link) use ($splFileInfo) {
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
                    if (basename($splFileInfo->getPath()) != 'htmlhelp') {
                        $href->nodeValue = '../_static/' . $fileName;
                    } else {
                        $href->nodeValue = '_static/' . $fileName;
                    }
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
        if (basename($splFileInfo->getPath()) != 'reference') {
            return;
        }
        $iFrames = $dom->getElementsByTagName('iframe');

        $this->loopNodeListCallback($iFrames, function (\DOMElement $iFrame) use ($dom) {
            $src = $iFrame->attributes->getNamedItem('src')->nodeValue;
            $anchor = $dom->createElement('a', $src);
            $anchor->setAttribute('href', $src);
            $anchor->setAttribute('target', '_blank');
            $anchor->setAttribute('class', 'reference external');
            $paragraph = $dom->createElement('p');
            $paragraph->appendChild($anchor);
            $iFrame->parentNode->replaceChild($paragraph, $iFrame);
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

        $this->loopNodeListCallback($scripts, function (\DOMElement $script) {
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
     * remove header and footer
     *
     * @param \DOMDocument $dom
     */
    public function removeHeaderFooter(\DOMDocument $dom)
    {
        $footer = $dom->getElementById('footer');
        if ($footer) {
            $footer->parentNode->removeChild($footer);
        }

        $header = $dom->getElementById('header');
        if ($header) {
            $header->parentNode->removeChild($header);
        }

        $xpath = new \DOMXPath($dom);
        $preFooter = $xpath->evaluate('//div[@class="prefooter"]');
        if ($preFooter->length) {
            $node = $preFooter->item(0);
            $node->parentNode->removeChild($node);
        }


    }

    /**
     * @param \DOMDocument|string $dom
     * @return string
     */
    public function replaceEncodingMeta(\DOMDocument $dom)
    {
        $metaNodes = $dom->getElementsByTagName('meta');
        $contentTypeMeta = $metaNodes->item(0);
        $contentAttr = $contentTypeMeta->attributes->getNamedItem('content');
        if ($contentAttr && $contentAttr->nodeValue == 'text/html; charset=utf-8') {
            /*
             * change the charset in meta the content will be convert encoding automatically.
             */
            $contentAttr->nodeValue = 'text/html; charset=gb2312';
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
            $filePath = $script->getDirectory() . '/_static/' . $fileName;
            if (stream_resolve_include_path($filePath)) {
                continue;
            }
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
            file_put_contents($filePath, $content);
        }
    }

    /**
     * @param string $html
     * @return string
     */
    public function removeComments($html)
    {
        return preg_replace('#(    )?<!--[<!\w\s\d="\':/.?+,&->\[\]]+-->#', '', $html);
    }

    /**
     * @inheritDoc
     */
    public function setEventsManager(ManagerInterface $eventsManager)
    {
        $this->eventsManager = $eventsManager;
    }

    /**
     * @inheritDoc
     */
    public function getEventsManager()
    {
        return $this->eventsManager;
    }

    /**
     * @param Script $script
     */
    public function cleanNoNeededStatic(Script $script)
    {
        $staticNoNeeded = require __DIR__ . '/../../data/needCleanStaticFileList.php';
        $directory =$script->getDirectory();

        foreach ($staticNoNeeded as &$filePath) {
            $fullPath = $directory . '/_static/' . $filePath;
            if (stream_resolve_include_path($fullPath)) {
                if (unlink($fullPath)) {
                    echo 'removed static ' , $filePath , PHP_EOL;
                }
            }

            $filePath = "\n_static\\" . str_replace('/', '\\', $filePath);
        }

        $hhpFile = $directory . '/PhalconDocumentationdoc.hhp';
        $hhpFileContent = file_get_contents($hhpFile);
        $hhpFileContent = str_replace($staticNoNeeded, '', $hhpFileContent);
        file_put_contents($hhpFile, $hhpFileContent);
    }
}
