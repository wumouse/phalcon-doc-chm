<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse\Index;

/**
 * @package Wumouse\Index
 */
class Item
{
    /**
     * @var string
     */
    protected $index;

    /**
     * @var string
     */
    protected $link;

    /**
     * @param string $index
     * @param string $link
     */
    public function __construct($index, $link)
    {
        $this->index = $index;
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }
}
