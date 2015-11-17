<?php
/**
 * phalcon-doc-chm.
 *
 * @author Haow1 <haow1@jumei.com>
 * @version $Id$
 */

namespace Wumouse;

use Wumouse\Index\Item;

/**
 * @package Wumouse
 */
class Index
{
    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var \SplFileInfo
     */
    protected $splFileInfo;

    /**
     * @var Item[]
     */
    protected $constants = [];

    /**
     * @var Item[]
     */
    protected $methods = [];

    /**
     * @var Item[]
     */
    protected $classes = [];

    /**
     * @return Index\Item[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @return Index\Item[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return Index\Item[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param \DOMDocument $dom
     * @param \SplFileInfo $splFileInfo
     */
    public function handle(\DOMDocument $dom, \SplFileInfo $splFileInfo)
    {
        $this->dom = $dom;
        $this->splFileInfo = $splFileInfo;

        $className = $this->getClassName();

        $this->classes[] = new Item($className, $splFileInfo->getFilename());
        $reflectionClass = new \ReflectionClass($className);
        $this->classes[] = new Item($reflectionClass->getShortName(), $splFileInfo->getFilename());

        $this->handleConstants($className);
        $this->handleMethods($className);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        /** @var \DOMElement $titleNode */
        $titleNode = $this->dom->getElementsByTagName('h1')->item(0);
        $classNode = $titleNode->getElementsByTagName('strong');

        if (!$classNode->length) {
            throw new \RuntimeException("class name not found in {$this->splFileInfo->getPathname()}");
        }

        return $classNode->item(0)->nodeValue;
    }

    /**
     * @param string $className
     * @return array
     */
    public function handleConstants($className)
    {
        $dom = $this->dom;

        $constantsNode = $dom->getElementById('constants');
        if ($constantsNode) {
            /** @var \DOMElement[] $constNodes */
            $constNodes = $constantsNode->getElementsByTagName('strong');

            foreach ($constNodes as $constNode) {
                $const = $constNode->nodeValue;
                $constWithClass = "{$className}::{$const}";

                $this->constants[] = new Item($constWithClass, $this->splFileInfo->getFilename());

                $constNode->parentNode->appendChild($dom->createElement('em', ' ' . constant($constWithClass)));
            }
        }
    }

    /**
     * @param string $className
     * @return array
     */
    public function handleMethods($className)
    {
        $dom = $this->dom;
        $indexesMethods = [];

        $methodsNode = $dom->getElementById('methods');
        if ($methodsNode) {
            /** @var \DOMElement[] $methodNodes */
            $methodNodes = $methodsNode->getElementsByTagName('p');

            foreach ($methodNodes as $methodNode) {
                $methodsNameNode = $methodNode->getElementsByTagName('strong');
                if ($methodsNameNode->length) {
                    $methodNameNode = $methodsNameNode->item(0);
                    $method = $methodNameNode->nodeValue;

                    $idAttr = $dom->createAttribute('id');
                    $idAttr->value = $method;

                    $methodNameNode->appendChild($idAttr);

                    $this->methods[] = new Item(
                        "{$className}::{$method}",
                        "{$this->splFileInfo->getFilename()}#{$method}"
                    );
                }
            }
        }

        return $indexesMethods;
    }
}
