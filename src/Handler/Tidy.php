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
 * tidy
 *
 * @package Wumouse\Handler
 * @deprecated
 */
class Tidy extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function iterating(Event $event, Script $script, File $file)
    {
        echo "Tidy is not compatible for this moment, just fix the theme of phalcon_doc will be work well";
        $event->stop();
        return false;
    }

    /**
     * use html-tidy to fix html content
     *
     * @see http://www.html-tidy.org/
     * @deprecated has some problem
     * @param Event $event
     * @param File $file
     * @return bool
     */
    public function html5Tidy(Event $event, File $file)
    {
        $tidyCommand = 'tidy';

        if (PHP_OS == 'WINNT') {
            $tidyCommand .= '.exe';
        }

        if (!is_executable($tidyCommand)) {
            echo 'html-tidy required, see http://www.html-tidy.org/' , PHP_EOL;
            $event->stop();
            return false;
        }

        $options = [
            '-f tidy_error.txt',
            '--numeric-entities true',
            '--newline CRLF',
            '--output-xhtml true',
        ];

        array_unshift($options, $tidyCommand);
        array_push($options, $file->getSplFileInfo()->getPathname());

        /*
         * when i use system to execute command ,the output will get an extra </html> after content
         * so use popen() instead
         */
        $handler = popen(implode(' ', $options), 'r');
        $file->setContent(stream_get_contents($handler));
        return true;
    }

    /**
     * @param File $file
     */
    public function html4Tidy(File $file)
    {
        $tidy = new \tidy();

        $content = $tidy->repairString(
            $file->getContent(),
            array(
                'numeric-entities' => true,
                'output-xhtml' => true,
                'newline' => 1,// 换行符 \r\n
            ),
            'utf8'
        );

        $file->setContent($content);
    }
}
