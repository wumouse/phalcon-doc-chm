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
class ToGb2312 extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $splFileInfo = $file->getSplFileInfo();

        $signFile = $splFileInfo->getPathname() . ToUtf8::getBakExtension();

        if (stream_resolve_include_path($signFile)) {
            $content = mb_convert_encoding($file->getContent(), 'CP936', 'UTF-8');
            $file->setContent($content);
            rename($signFile, $splFileInfo->getPathname() . self::getBakExtension());
        }
    }

    /**
     * @return string
     */
    public static function getBakExtension()
    {
        return '.gb2312';
    }
}
