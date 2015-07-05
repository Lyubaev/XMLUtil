<?php namespace Lyubaev\Tests\XMLUtil;

use Lyubaev\XMLUtil\Reader;
use Lyubaev\XMLUtil\Exception\InvalidArgumentException;
use Lyubaev\XMLUtil\Exception\DomainException;

class ExceptionOffsetTest extends \PHPUnit_Framework_TestCase
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
        $this->iterator->setOffset(.5);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testString()
    {
        $this->iterator->setOffset('string');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNull()
    {
        $this->iterator->setOffset(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean()
    {
        $this->iterator->setOffset(true);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean2()
    {
        $this->iterator->setOffset(false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testArray()
    {
        $this->iterator->setOffset([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testObject()
    {
        $this->iterator->setOffset(json_decode('{}'));
    }

    /**
     * @expectedException DomainException
     */
    public function testNegativeOffset()
    {
        $this->iterator->setOffset(-1);
    }

}