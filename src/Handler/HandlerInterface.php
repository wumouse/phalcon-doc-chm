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
interface HandlerInterface
{
    /**
     * 迭代事件接收方法
     *
     * @param Event $event
     * @param Script $script
     * @param File $file
     * @return bool|null
     */
    public function iterating(Event $event, Script $script, File $file);
}
