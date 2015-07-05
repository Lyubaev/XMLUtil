<?php namespace Lyubaev\Tests\XMLUtil;

use Lyubaev\XMLUtil\Reader;

class IteratorTest extends \PHPUnit_Framework_TestCase
{
    public $iterator;

    public function setUp()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <node>0</node>
    <node>1</node>
    <node>2</node>
    <node>3</node>
    <node>4</node>
</root>
XML;
        $this->iterator = new Reader;
        $this->iterator->xml($xml);
    }

    public function testLimit()
    {
        $count = 0;
        $this->iterator->setLimit(1);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(1, $count);

        $count = 0;
        $this->iterator->setLimit(4);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(4, $count);

        $count = 0;
        $this->iterator->setLimit(10);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(5, $count);
    }

    public function testOffset()
    {
        $count = 0;
        $this->iterator->setOffset(0);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(5, $count);

        $count = 0;
        $this->iterator->setOffset(2);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(3, $count);

        $count = 0;
        $this->iterator->setOffset(10);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(0, $count);

    }

    public function testStep()
    {
        $count = 0;
        $this->iterator->setStep(1);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(5, $count);

        $count = 0;
        $this->iterator->setStep(2);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(3, $count);

        $count = 0;
        $this->iterator->setStep(3);
        foreach ($this->iterator->__invoke() as $val) $count +=1;
        $this->assertEquals(2, $count);
    }
}