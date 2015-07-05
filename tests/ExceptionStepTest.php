<?php namespace Lyubaev\Tests\XMLUtil;

use Lyubaev\XMLUtil\Reader;
use Lyubaev\XMLUtil\Exception\InvalidArgumentException;
use Lyubaev\XMLUtil\Exception\DomainException;

class ExceptionStepTest extends \PHPUnit_Framework_TestCase
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
        $this->iterator->setStep(.5);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testString()
    {
        $this->iterator->setStep('string');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNull()
    {
        $this->iterator->setStep(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean()
    {
        $this->iterator->setStep(true);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoolean2()
    {
        $this->iterator->setStep(false);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testArray()
    {
        $this->iterator->setStep([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testObject()
    {
        $this->iterator->setStep(json_decode('{}'));
    }

    /**
     * @expectedException DomainException
     */
    public function testNegativeStep()
    {
        $this->iterator->setStep(-1);
    }

    /**
     * @expectedException DomainException
     */
    public function testZeroOffset()
    {
        $this->iterator->setStep(0);
    }

}