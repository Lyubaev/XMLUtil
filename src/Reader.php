<?php
/**
 * XMLUtil
 *
 * @author    Kirill Lyubaev <lubaev.ka@gmail.com>
 * @copyright Copyright (c) 2015 Kirill Lyubaev
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD New
 */

namespace Lyubaev\XMLUtil;

use XMLReader;
use DOMDocument;
use Lyubaev\XMLUtil\Exception\InvalidArgumentException;
use Lyubaev\XMLUtil\Exception\RuntimeException;
use Lyubaev\XMLUtil\Exception\DomainException;

class Reader
{
    const DEF_DEPTH = 1;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $source;

    /**
     * @var int 1 or 2
     */
    private $isFileOrSource;

    private static $setFile = 1;
    private static $setSource = 2;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var int
     */
    private $currentPosition;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $step;

    /**
     * @var int
     */
    private $depth = self::DEF_DEPTH;

    public function __construct($version = '1.0', $encoding = 'UTF-8')
    {
        $this->currentPosition = 0;

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

    /**
     * Set the URI containing the XML to parse.
     *
     * @param string $file
     */
    public function open($file)
    {
        if (is_string($file)) {
            $this->file = $file;
            $this->setSourceType(self::$setFile);
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be string, %s given',
                __METHOD__,
                gettype($file)
            )
        );
    }

    /**
     * Set the data containing the XML to parse.
     *
     * @param string $source
     */
    public function load($source)
    {
        if (is_string($source)) {
            $this->source = $source;
            $this->setSourceType(self::$setSource);
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Method %s expects parameter 1 to be string, %s given',
                __METHOD__,
                gettype($source)
            )
        );
    }

    public function parse(array $options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        switch ($this->getSourceType()) {
            case self::$setFile:
                $reader = new XMLReader;
                $result = $reader->open($this->file, $this->getEncoding());
                break;
            case self::$setSource:
                $reader = new XMLReader;
                $result = $reader->XML($this->source, $this->getEncoding());
                break;
            default:
                throw new RuntimeException('Data unload');
        }

        if (!$result) {
            throw new RuntimeException('Unable to open source data');
        }

        $this->moveToDepth($reader, $this->depth);
        $nextNode = $reader->localName;

        $document = null;
        if ($this->makeOffset($reader, $nextNode)) {
            do {
                $document = null;
                if ($this->itLimit()) {
                    break;
                }

                $document = new DOMDocument($this->getVersion(), $this->getEncoding());
                $document->preserveWhiteSpace = false;
                $document->formatOutput = false;
                $document->recover = true;

                $xml = $reader->readOuterXml();
                $document->loadXML($xml, LIBXML_COMPACT | LIBXML_NOEMPTYTAG);

                yield $this->currentPosition() => $document->documentElement;

                if (!$this->makeStep($reader, $nextNode)) {
                    break;
                }

                $this->consume();
            } while($reader->next($nextNode));
        }

        unset($document);
        $reader->close();
    }

    private function currentPosition()
    {
        return $this->currentPosition;
    }

    private function consume()
    {
        ++$this->currentPosition;
    }

    private function setSourceType($type)
    {
        $this->isFileOrSource = $type;
    }

    private function getSourceType()
    {
        return $this->isFileOrSource;
    }

    /**
     * Set options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $options = array_change_key_case($options);

        if (array_key_exists('limit', $options)) {
            $this->setLimit($options['limit']);
        }

        if (array_key_exists('offset', $options)) {
            $this->setOffset($options['offset']);
        }

        if (array_key_exists('step', $options)) {
            $this->setStep($options['step']);
        }

        if (array_key_exists('depth', $options)) {
            $this->setDepth($options['depth']);
        }
    }

    /**
     * Set limit.
     *
     * @param int $value
     */
    public function setLimit($value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Method %s expects parameter 1 to be int, %s given',
                    __METHOD__,
                    gettype($value)
                )
            );
        }

        if ($value < 0) {
            throw new DomainException("The limit must be a positive value, $value given");
        }

        $this->limit = $value;
    }

    /**
     * Set offset.
     *
     * @param int $value
     */
    public function setOffset($value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Method %s expects parameter 1 to be int, %s given',
                    __METHOD__,
                    gettype($value)
                )
            );
        }

        if ($value < 0) {
            throw new DomainException("The offset must be a positive value, $value given");
        }

        $this->offset = $value;
    }

    /**
     * Set step.
     *
     * @param int $value
     */
    public function setStep($value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Method %s expects parameter 1 to be int, %s given',
                    __METHOD__,
                    gettype($value)
                )
            );
        }

        if ($value < 1) {
            throw new DomainException("Step must be greater than 0, $value given");
        }

        $this->step = $value;
    }

    /**
     * Set depth.
     *
     * @param int $value
     */
    public function setDepth($value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Method %s expects parameter 1 to be int, %s given',
                    __METHOD__,
                    gettype($value)
                )
            );
        }

        if ($value < 0) {
            throw new DomainException("The depth must be a positive value, $value given");
        }

        $this->depth = $value;
    }

    private function makeOffset(XMLReader $reader, $baseName = '')
    {
        while ($this->offset--) {
            if (!$reader->next($baseName)) {
                return false;
            }
            $this->consume();
        }

        return true;
    }

    private function makeStep(XMLReader $reader, $baseName)
    {
        // If step > 1
        for ($step = $this->step; --$step;) {
            if (!$reader->next($baseName)) {
                return false;
            }
            $this->consume();
        }

        return true;
    }


    private function itLimit()
    {
        if (null !== $this->limit && !$this->limit--) {
            return true;
        }

        return false;
    }

    /**
     * Move pointer to depth.
     *
     * @param XMLReader $reader
     * @param int       $depth
     */
    protected function moveToDepth(XMLReader $reader, $depth)
    {
        do {
            if ($reader->nodeType === XMLReader::ELEMENT) {
                if ($reader->depth === $depth) {
                    return;
                }
            }
        } while ($reader->read());
    }
}