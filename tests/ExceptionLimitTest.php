<?php namespace Lyubaev\Tests\XMLUtil;

use Lyubaev\XMLUtil\Reader;
use Lyubaev\XMLUtil\Exception\InvalidArgumentException;
use Lyubaev\XMLUtil\Exception\DomainException;

class ExceptionLimitTest extends \PHPUnit_Framework_TestCase
{
    public $iterator;

    public function setUp()
    {
        $this->iterator = new Reader;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFloat()
    {
        $this->iterator->setLimit(.5);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testString()
    {
        $this->iterator->setLimit('string');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNull()
    {
        $this->iterator->setLimit(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean()
    {
        $this->iterator->setLimit(true);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean2()
    {
        $this->iterator->setLimit(false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testArray()
    {
        $this->iterator->setLimit([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testObject()
    {
        $this->iterator->setLimit(json_decode('{}'));
    }

    /**
     * @expectedException DomainException
     */
    public function testNegativeLimit()
    {
        $this->iterator->setLimit(-1);
    }

}