<?php namespace Lyubaev\XMLUtil\Validator;

use Lyubaev\XMLUtil\Exception\InvalidArgumentException;
use Lyubaev\XMLUtil\Exception\RuntimeException;

class Relax implements ValidatorInterface
{
    private $file;
    private $source;
    private $isFileOrSource;
    private static $setFile = 1;
    private static $setSource = 2;

    /**
     * @var array
     */
    private $error;

    public function __construct()
    {
        $this->error = [];

        // TODO: Возможно следует устанавливать обработчик ошибок здесь!
    }

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

    public function source($source)
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

    private function setSourceType($type)
    {
        $this->isFileOrSource = $type;
    }

    private function getSourceType()
    {
        return $this->isFileOrSource;
    }

    public function isValid(\DOMDocument $object)
    {
        set_error_handler([$this, 'errorHandler']);
        switch ($this->getSourceType()) {
            case self::$setFile:
                return $object->relaxNGValidate($this->file);
            case self::$setSource:
                return $object->relaxNGValidateSource($this->source);
            default:
                throw new RuntimeException('Validation scheme is not loaded!');
        }
        restore_error_handler();
    }

    public function getMessages()
    {
        return $this->error;
    }

    public function errorHandler($errno, $errstr)
    {
        if (substr_count($errstr, 'DOMDocument::relaxNG') > 0) {
            $this->error []= [$errno, $errstr];
            return true;
        }
        return false;
    }
}