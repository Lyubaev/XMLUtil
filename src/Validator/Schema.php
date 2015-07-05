<?php
/**
 * This file is part of the XMLUtil package.
 *
 * @link      https://github.com/Lyubaev/XMLUtil
 * @copyright Copyright (c) 2015 Kirill Lyubaev
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD New
 */

namespace Lyubaev\XMLUtil\Validator;

/**
 * Class Schema
 *
 * @package XMLUtil
 */
class Schema implements ValidatorInterface
{
    private $file;
    private $source;
    private $isFileOrSource;
    private static $setFile = 1;
    private static $setSource = 2;

    private $flag;
    private $error = [];

    public function open($file, $flag = 0)
    {
        $this->file = $file;
        $this->flag = $flag;
        $this->isFileOrSource = self::$setFile;
    }

    public function load($source, $flag = 0)
    {
        $this->source = $source;
        $this->flag =$flag;
        $this->isFileOrSource = self::$setSource;
    }

    public function isValid(\DOMDocument $object)
    {
        switch ($this->isFileOrSource) {
            case self::$setFile:
                return $object->schemaValidate($this->file, $this->flag);
            case self::$setSource:
                return $object->schemaValidateSource($this->source, $this->flag);
            default:
                return null;
        }
    }

    public function getMessages()
    {
        return $this->error;
    }
}