<?php
/**
 * XMLUtil
 *
 * @author    Kirill Lyubaev <lubaev.ka@gmail.com>
 * @copyright Copyright (c) 2015 Kirill Lyubaev
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD New
 */

namespace Lyubaev\XMLUtil;

use DOMDocument;
use XSLTProcessor;
use Lyubaev\XMLUtil\Exception\RuntimeException;
use Lyubaev\XMLUtil\Exception\InvalidArgumentException;

final class XSLTransform
{
    private $xslt;
    private $document;

    private static $instance;

    private function __construct()
    {
        $this->createXSLTProcessor();
        $this->createDocument();
    }

    private function createXSLTProcessor()
    {
        $this->xslt = new XSLTProcessor;
        if (!$this->xslt->hasExsltSupport()) {
            throw new RuntimeException('EXSLT support not available');
        }
    }

    private static function getXSLTProcessor()
    {
        return self::getInstance()->xslt;
    }

    private function createDocument()
    {
        $this->document = new DOMDocument;
    }

    private static function getDocument()
    {
        return self::getInstance()->document;
    }

    private static function getInstance()
    {
        if (null===self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public static function open($uri)
    {
        if (is_string($uri)) {
            self::getDocument()->load($uri);
            return self::import(self::getDocument());
        }

        throw new InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be string, %s given',
                __METHOD__,
                gettype($uri)
            )
        );
    }

    public static function load($source)
    {
        if (is_string($source)) {
            self::getDocument()->loadXML($source);
            return self::import(self::getDocument());
        }

        throw new InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be string, %s given',
                __METHOD__,
                gettype($source)
            )
        );
    }

    public static function import($document)
    {
        if (is_object($document)) {
            self::getXSLTProcessor()->importStylesheet($document);
            return self::getXSLTProcessor();
        }

        throw new InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be object, %s given',
                __METHOD__,
                gettype($document)
            )
        );
    }
}