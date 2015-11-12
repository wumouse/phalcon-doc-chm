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
            'encoding',
            'main',
        );

        $this->options = getopt('', $longOptionsDef);
    }

    /**
     * @return string
     */
    public function getOptionsDescription()
    {
        $descriptions = array(
            'backup' => '备份',
            'restore' => '恢复',
            'encoding' => '转换编码',
            'main' => '主要任务, 处理 HTML 内容',
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
