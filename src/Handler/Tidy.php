<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse\Handler;

use Phalcon\Events\Event;
use Wumouse\File;
use Wumouse\Script;

/**
 * @package Wumouse\Handler
 */
class Tidy extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $tidy = new \tidy();

        $content = $tidy->repairString(
            $file->getContent(),
            array(
                'numeric-entities' => true,
                'output-xhtml' => true,
                'newline' => 1,// 换行符 \r\n
            ),
            'utf8'
        );

        $file->setContent($content);
    }
}
