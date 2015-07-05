<?php namespace Lyubaev\Tests\XMLUtil;

use Lyubaev\XMLUtil\Reader;
use Lyubaev\XMLUtil\Exception\InvalidArgumentException;
use Lyubaev\XMLUtil\Exception\DomainException;

class ExceptionDepthTest extends \PHPUnit_Framework_TestCase
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
        $this->iterator->setDepth(.5);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testString()
    {
        $this->iterator->setDepth('string');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNull()
    {
        $this->iterator->setDepth(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean()
    {
        $this->iterator->setDepth(true);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean2()
    {
        $this->iterator->setDepth(false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testArray()
    {
        $this->iterator->setDepth([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testObject()
    {
        $this->iterator->setDepth(json_decode('{}'));
    }

    /**
     * @expectedException DomainException
     */
    public function testNegativeDepth()
    {
        $this->iterator->setDepth(-1);
    }

}