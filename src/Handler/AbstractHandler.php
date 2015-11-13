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
abstract class AbstractHandler implements HandlerInterface
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    abstract public function iterating(Event $event, Script $script, File $file);

    /**
     * 开始迭代前
     *
     * @param Event $event
     * @param Script $script
     */
    public function beforeIterate(Event $event, Script $script)
    {
    }

    /**
     * 完成迭代后
     *
     * @param Event $event
     * @param Script $script
     */
    public function afterIterate(Event $event, Script $script)
    {
    }
}
