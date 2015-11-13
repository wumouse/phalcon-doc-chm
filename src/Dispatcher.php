<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse;

use Phalcon\DiInterface;
use Phalcon\Events\Manager;

/**
 * @package Wumouse
 */
class Dispatcher
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var DiInterface
     */
    protected $dependencyInjector;

    /**
     * @param DiInterface $dependencyInjector
     */
    public function __construct(DiInterface $dependencyInjector)
    {
        $this->dependencyInjector = $dependencyInjector;
        $longOptionsDef = array(
            'backup',
            'restore',
            'toUtf8',
            'toGb2312',
            'tidy',
            'main',
            'clean'
        );

        $this->options = getopt('', $longOptionsDef);
    }

    /**
     * @return string
     */
    public function getOptionsDescription()
    {
        $descriptions = array(
            'backup' => 'backup html',
            'restore' => 'restore from backup',
            'toUtf8' => 'convert encoding from gb2312 to utf-8',
            'toGb2312' => 'convert encoding from utf-8 to gb2312',
            'tidy' => 'tidy incomplete html',
            'main' => 'parse html dom , and then remove some tag cause document' .
                ' load slowly, parse and generate index for chm',
            'clean' => 'clean the sign files, but execute always before and after the loop'
        );

        $response = 'Notice: The order of arguments passed is the execute order' . PHP_EOL . PHP_EOL;

        foreach ($descriptions as $name => $description) {
            $response .= sprintf(
                '--%-20s %s %s',
                $name,
                $description,
                PHP_EOL
            );
        }

        return $response;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function dispatch(array $options)
    {
        /** @var Manager $eventsManager */
        $eventsManager = $this->dependencyInjector->getShared('eventsManager');
        foreach ($options as $option => $value) {
            $reflection = new \ReflectionClass('Wumouse\Handler\\' . $option);
            $handler = $reflection->newInstanceArgs(array($value));
            $eventsManager->attach('application', $handler);
        }
    }
}
