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
class Save extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        $file->save();
    }

}
