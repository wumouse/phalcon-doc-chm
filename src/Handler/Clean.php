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
class Clean extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
    }

    /**
     * @inheritDoc
     */
    public function beforeIterate(Event $event, Script $script)
    {
        $this->run($script);
    }

    /**
     * @inheritDoc
     */
    public function afterIterate(Event $event, Script $script)
    {
        $this->run($script);
    }

    /**
     * @param Script $script
     */
    public function run(Script $script)
    {
        echo 'cleaning *.utf8 *.gb2312', PHP_EOL;

        $lastWorkDir = getcwd();

        chdir($script->getDirectory());

        $format = PHP_OS == 'WINNT' ? 'del /S /Q .\*%s' : 'find . -name %s | xargs rm -r';
        passthru(sprintf($format, ToGb2312::getBakExtension()));
        passthru(sprintf($format, ToUtf8::getBakExtension()));

        chdir($lastWorkDir);
    }
}
