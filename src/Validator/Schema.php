<?php namespace Lyubaev\XMLUtil\Validator;

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

    public function source($source, $flag = 0)
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