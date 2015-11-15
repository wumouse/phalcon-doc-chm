<?php
/**
 * Created by PhpStorm.
 * User: wumouse
 * Date: 2015/11/15
 * Time: 10:07
 */

namespace Wumouse\Handler;


use Phalcon\Events\Event;

/**
 * @package Wumouse\Handler
 */
class Author extends AbstractMainHandler
{

    /**
     * @inheritDoc
     */
    public function beforeChangeDom(Event $event, Main $main, \DOMDocument $dom)
    {
    }

    /**
     * @inheritDoc
     */
    public function afterChangeDom(Event $event, Main $main, array $data)
    {
        /** @var \DOMDocument $dom */
        /** @var \SplFileInfo $splFileInfo */

        list($dom, $splFileInfo) = $data;
        if (false === strpos($splFileInfo->getPathname(), 'htmlhelp' . DIRECTORY_SEPARATOR . 'index.html')) {
            return;
        }
        $authorBlockNodes = $dom->getElementById('chm-author');
        if (!$authorBlockNodes) {
            $welcomeSection = $dom->getElementById('welcome');
            if (!$welcomeSection) {
                return;
            }

            $tmpDom = new \DOMDocument();
            $tmpDom->loadHTMLFile(__DIR__ . '/../../view/readme.html');
            $newNode = $dom->importNode($tmpDom->getElementById('chm-author'), true);
            $welcomeSection->parentNode->insertBefore($newNode, $welcomeSection);
        }
    }
}
