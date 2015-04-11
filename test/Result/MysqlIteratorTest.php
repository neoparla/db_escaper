<?php

namespace NeoParla\DbEscaper\Result;

use mysqli_result;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;

class MysqlIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mysqli_result;

    /**
     * @test
     */
    public function correctImplements()
    {
        $fixture = $this->getFixture();

        $this->assertInstanceOf(__NAMESPACE__ . '\DbIterator', $fixture);
        $this->assertInstanceOf('\Iterator', $fixture);
        $this->assertInstanceOf('\Countable', $fixture);
        $this->assertInstanceOf('\ArrayAccess', $fixture);
    }

    /**
     * @test
     */
    public function iteratorImplementationRewind()
    {
        $fixture = $this->getFixture();
        $this->mysqli_result
            ->expects($this->once())
            ->method('data_seek')
            ->with($this->identicalTo(0))
        ;

        $value = rand(0,1000);
        $this->mysqli_result
            ->expects($this->once())
            ->method('fetch_assoc')
            ->will($this->returnValue($value))
        ;

        $fixture->rewind();

        $this->assertAttributeSame(0, 'index', $fixture);
        $this->assertAttributeSame($value, 'row', $fixture);
    }

    /**
     * @test
     */
    public function iteratorImplementationNext()
    {
        $fixture = $this->getFixture();
        $original_index = rand(0, 100);

        $index_property = new ReflectionProperty($fixture, 'index');
        $index_property->setAccessible(true);
        $index_property->setValue($fixture, $original_index);

        $value = rand(0,1000);
        $this->mysqli_result
            ->expects($this->once())
            ->method('fetch_assoc')
            ->will($this->returnValue($value))
        ;

        $fixture->next();

        $this->assertAttributeSame($original_index + 1, 'index', $fixture);
        $this->assertAttributeSame($value, 'row', $fixture);
    }

    /**
     * @test
     */
    public function arrayAccessImplementationOffsetExistsWhenExists()
    {
        $fixture = $this->getFixture();
        $original_index = rand(0,100);
        $desired_offset = $original_index + rand(1,100);

        $index_property = new ReflectionProperty($fixture, 'index');
        $index_property->setAccessible(true);
        $index_property->setValue($fixture, $original_index);

        $this->mysqli_result
            ->expects($this->any())
            ->method('data_seek')
            ->will($this->returnValue(true));

        $this->mysqli_result
            ->expects($this->at(0))
            ->method('data_seek')
            ->with($desired_offset);

        $this->mysqli_result
            ->expects($this->at(1))
            ->method('data_seek')
            ->with($original_index);

        $this->assertTrue($fixture->offsetExists($desired_offset));
    }

    /**
     * @test
     */
    public function arrayAccessImplementationOffsetExistsWhenNotExists()
    {
        $fixture = $this->getFixture();
        $original_index = rand(0,100);
        $desired_offset = $original_index + rand(1,100);

        $index_property = new ReflectionProperty($fixture, 'index');
        $index_property->setAccessible(true);
        $index_property->setValue($fixture, $original_index);

        $this->mysqli_result
            ->expects($this->any())
            ->method('data_seek')
            ->will($this->returnValue(false));

        $this->mysqli_result
            ->expects($this->at(0))
            ->method('data_seek')
            ->with($desired_offset);

        $this->mysqli_result
            ->expects($this->at(1))
            ->method('data_seek')
            ->with($original_index);

        $this->assertFalse($fixture->offsetExists($desired_offset));
    }

    /**
     * @test
     */
    public function arrayAccessImplementationOffsetGetWhenExists()
    {
        $fixture = $this->getFixture();
        $desired_offset = rand(1,100);
        $value = rand(1,100);

        $this->mysqli_result
            ->expects($this->once())
            ->method('data_seek')
            ->with($desired_offset)
            ->will($this->returnValue(true));

        $this->mysqli_result
            ->expects($this->once())
            ->method('fetch_assoc')
            ->will($this->returnValue($value));

        $this->assertSame($value, $fixture->offsetGet($desired_offset));
    }

    /**
     * @test
     */
    public function arrayAccessImplementationOffsetGetWhenNotExists()
    {
        $fixture = $this->getFixture();
        $desired_offset = rand(1,100);

        $this->mysqli_result
            ->expects($this->once())
            ->method('data_seek')
            ->with($desired_offset)
            ->will($this->returnValue(false));

        $this->mysqli_result
            ->expects($this->never())
            ->method('fetch_assoc');

        $this->assertFalse($fixture->offsetGet($desired_offset));
    }

    /**
     * @return MysqlIterator
     */
    private function getFixture()
    {
        $this->mysqli_result = $this->getMockBuilder('\mysqli_result')
            ->setMethods(array('data_seek', 'fetch_assoc'))
            ->disableOriginalConstructor()
            ->getMock();

        $fixture = new MysqlIterator($this->mysqli_result);
        return $fixture;
    }
}