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
 * @package Handler
 */
class ToUtf8 extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $splFileInfo = $file->getSplFileInfo();

        $signFile = $splFileInfo->getPathname() . self::getBakExtension();

        if (!stream_resolve_include_path($signFile)) {
            $content = mb_convert_encoding($file->getContent(), 'UTF-8', 'CP936');
            $file->setContent($content);

            touch($signFile);
        }
    }

    /**
     * @return string
     */
    public static function getBakExtension()
    {
        return '.utf8';
    }
}
