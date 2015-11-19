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
use Phalcon\Mvc\View;

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
        $dependencyInjector->setShared('eventsManager', 'Phalcon\Events\Manager');

        $dependencyInjector->setShared('view', function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/../view/');
            $view->registerEngines([
                '.php' => 'Phalcon\Mvc\View\Engine\Php',
                '.html' => 'Phalcon\Mvc\View\Engine\Volt',
            ]);

            return $view;
        });

        $dispatcher = new Dispatcher($dependencyInjector);

        $dependencyInjector->set('dispatcher', $dispatcher);

        $this->dependencyInjector = $dependencyInjector;
        $options = $dispatcher->getOptions();

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

        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->dependencyInjector->get('dispatcher');

        $options = $dispatcher->getOptions();
        if (empty($options)) {
            echo $dispatcher->getOptionsDescription();
            return;
        }

        echo "executed commands: ", implode(', ', array_keys($options)) , PHP_EOL;

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
            if ($splFileInfo->getExtension() != 'html') {
                continue;
            }
            $file = new File($splFileInfo);
            echo "Handing file: {$splFileInfo->getPathname()} ...", PHP_EOL;
            $continue = $eventsManager->fire('application:iterating', $this, $file, true);
            if (false === $continue) {
                return;
            }
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
