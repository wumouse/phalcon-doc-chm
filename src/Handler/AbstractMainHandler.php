<?php
/**
 * Created by PhpStorm.
 * User: wumouse
 * Date: 2015/11/15
 * Time: 10:10
 */

namespace Wumouse\Handler;

use Phalcon\Events\Event;

/**
 * @package Wumouse\Handler
 * @see Main
 */
abstract class AbstractMainHandler
{
    /**
     * @param Event $event
     * @param Main $main
     * @param \DOMDocument $dom
     * @return mixed
     */
    abstract public function beforeChangeDom(Event $event, Main $main, \DOMDocument $dom);

    /**
     * @param Event $event
     * @param Main $main
     * @param array $data
     * @return mixed
     */
    abstract public function afterChangeDom(Event $event, Main $main, array $data);
}
