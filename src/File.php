<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse;

/**
 * @package Wumouse
 */
class File
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var \SplFileInfo
     */
    protected $splFileInfo;

    /**
     * @param \SplFileInfo $splFileInfo
     */
    public function __construct(\SplFileInfo $splFileInfo)
    {
        $this->splFileInfo = $splFileInfo;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (!$this->content) {
            $this->content = file_get_contents($this->splFileInfo->getPathname());
        }
        return $this->content;
    }

    /**
     * @return \SplFileInfo clone one
     */
    public function getSplFileInfo()
    {
        return clone $this->splFileInfo;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException('argument content must be a string');
        }
        if ($content != $this->content) {
            $this->content = $content;
        }
    }

    /**
     * @param string|null $path
     * @return int
     */
    public function save($path = null)
    {
        if (!$path) {
            $path = $this->splFileInfo->getPathname();
        }
        return file_put_contents($path, $this->getContent());
    }
}
