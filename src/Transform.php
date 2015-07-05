<?php
/**
 * XMLUtil
 *
 * @author    Kirill Lyubaev <lubaev.ka@gmail.com>
 * @copyright Copyright (c) 2015 Kirill Lyubaev
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD New
 */

namespace Lyubaev\XMLUtil;

use DOMNode;
use DOMDocument;
use XSLTProcessor;

class Transform
{
    private $xslt;

    private $version;
    private $encoding;

    public function __construct($version = '1.0', $encoding = 'UTF-8')
    {
        $this->xslt = new XSLTProcessor;
        if (!$this->xslt->hasExsltSupport()) {
            throw new Exception\RuntimeException('EXSLT support not available');
        }

        $this->setVersion($version);
        $this->setEncoding($encoding);
    }

    private function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }

    private function setEncoding($encoding)
    {
        if (is_string($encoding)) {
            $this->encoding = $encoding;
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be string, %s given',
                __METHOD__,
                gettype($encoding)
            )
        );
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function open($file)
    {
        $document = new DOMDocument($this->getVersion(), $this->getEncoding());
        $document->load($file);
        $this->import($document);
    }

    public function source($source)
    {
        $document = new DOMDocument($this->getVersion(), $this->getEncoding());
        $document->loadXML($source);
        $this->import($document);
    }

    public function import($document)
    {
        if (is_object($document)) {
            $this->xslt->importStylesheet($document);
            return;
        }

        throw new Exception\InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be object, %s given',
                __METHOD__,
                gettype($document)
            )
        );
    }

    public function toXML($object)
    {
        if (is_object($object)) {
            return $this->xslt->transformToXml($object);
        }

        throw new Exception\InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be object, %s given',
                __METHOD__,
                gettype($object)
            )
        );
    }

    public function toDoc(DOMNode $node)
    {
        return $this->xslt->transformToDoc($node);
    }

    public function registerPHPFunctions($restrict = null)
    {
        if (null === $restrict) {
            $this->xslt->registerPHPFunctions();
        } else {
            $this->xslt->registerPHPFunctions($restrict);
        }
    }
}