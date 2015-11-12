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
use Phalcon\Http\Response;

/**
 */
class Script
{
    /**
     * @var DiInterface
     */
    protected $dependencyInjector;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @param DiInterface $dependencyInjector
     */
    public function __construct(DiInterface $dependencyInjector)
    {
        $this->dependencyInjector = $dependencyInjector;
        $dispatcher = new Dispatcher($dependencyInjector);
        $options = $dispatcher->getOptions();

        if (empty($options)) {
            echo $dispatcher->getOptionsDescription();
        }
        $dispatcher->dispatch($options);
    }

    /**
     * 执行
     *
     * @param $directory
     */
    public function run($directory)
    {
        if (!stream_resolve_include_path($directory)) {
            throw new \InvalidArgumentException("Directory: $directory not found");
        }

        $this->directory = $directory;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var Manager $eventsManager */
        $eventsManager = $this->dependencyInjector->getShared('eventsManager');

        $continue = $eventsManager->fire('application:beforeIterate', $this, null, true);
        if (false === $continue) {
            return;
        }

        /** @var \SplFileInfo $splFileInfo */
        foreach ($iterator as $splFileInfo) {
            $file = new File($splFileInfo);
            echo "Handing file: {$splFileInfo->getPathname()} ...", PHP_EOL;
            $eventsManager->fire('application:iterating', $this, $file, true);
            $file->save();
        }

        $eventsManager->fire('application:afterIterate', $this, null, true);
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return DiInterface
     */
    public function getDependencyInjector()
    {
        return $this->dependencyInjector;
    }
}
