<?php
/**
 * Created by PhpStorm.
 * User: wumouse
 * Date: 2015/11/15
 * Time: 15:50
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
