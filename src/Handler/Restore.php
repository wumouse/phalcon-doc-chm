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
class Restore extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $splFileInfo = $file->getSplFileInfo();

        $sourceFile = $splFileInfo->getPathname();
        $backupFile = $sourceFile . '.bak';

        if (stream_resolve_include_path($backupFile)) {
            copy($backupFile, $sourceFile);
        }
    }
}
