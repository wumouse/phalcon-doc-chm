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
class Backup extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $splFileInfo = $file->getSplFileInfo();
        if ($splFileInfo->isDir() || $splFileInfo->getExtension() != 'html') {
            return;
        }

        $backupFile = $splFileInfo->getPathname() . '.bak';

        if (!stream_resolve_include_path($backupFile)) {
            copy($splFileInfo->getPathname(), $backupFile);
        }
    }
}
