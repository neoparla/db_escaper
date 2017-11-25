<?php

namespace NeoParla\DbEscaper\Link;

use Exception;
use PHPUnit_Framework_MockObject_MockObject;

class MysqlTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function constructUsingMySqliObject()
    {
        $object = new MySqlDbEscaper();
        $this->assertAttributeInstanceOf(
            '\mysqli',
            'link',
            $object
        );
        $this->assertAttributeSame(false, 'is_connected', $object);
    }

    /** @test */
    public function settingConnectionDataDefaultPort()
    {
        $object = new MySqlDbEscaper();

        $object->setConnectionData($this->getStubbedConnectionData());

        $this->assertAttributeSame('host', 'host', $object);
        $this->assertAttributeSame('user', 'user', $object);
        $this->assertAttributeSame('pass', 'pass', $object);
        $this->assertAttributeSame('schema', 'schema', $object);
        $this->assertAttributeSame(MySqlDbEscaper::DEFAULT_PORT, 'port', $object);
        $this->assertAttributeSame(false, 'is_connected', $object);
    }

    /** @test */
    public function settingConnectionData()
    {
        $object = new MySqlDbEscaper();

        $data = $this->getStubbedConnectionData();
        $data['port'] = 'customized_port';

        $object->setConnectionData($data);

        $this->assertAttributeSame('customized_port', 'port', $object);
        $this->assertAttributeSame(false, 'is_connected', $object);
    }

    /** @test */
    public function connectIsConnectingThroughMySqli()
    {
        $object = $this->getFixture(array('real_connect', 'set_charset'));

        $mocked_link = $this->getMockedMysqliObject($object);

        $mocked_link
            ->expects($this->once())
            ->method('real_connect')
            ->with(
                'host', 'user', 'pass', 'schema', MySqlDbEscaper::DEFAULT_PORT
            );

        $mocked_link
            ->expects($this->once())
            ->method('set_charset')
            ->with(MySqlDbEscaper::DEFAULT_CHARSET);

        $mocked_link->connect_error = false;
        $object->connect();

        $this->assertAttributeSame(true, 'is_connected', $object);
        $this->assertAttributeCount(0, 'errors', $object);
    }

    /** @test */
    public function connectIsNotReconnectingTwice()
    {
        $object = $this->getFixture(array('real_connect', 'set_charset'));

        $mocked_link = $this->getMockedMysqliObject($object);

        $mocked_link
            ->expects($this->once())
            ->method('real_connect');

        $mocked_link
            ->expects($this->once())
            ->method('set_charset');

        $mocked_link->connect_error = false;
        $object->connect();
        $object->connect();

        $this->assertAttributeSame(true, 'is_connected', $object);
        $this->assertAttributeCount(0, 'errors', $object);
    }

    /** @test */
    public function connectIsConnectingThroughMySqliWhenError()
    {
        $object = $this->getFixture(array('real_connect', 'set_charset'));

        $mocked_link = $this->getMockedMysqliObject($object);

        $mocked_link
            ->expects($this->once())
            ->method('real_connect')
            ->with(
                'host', 'user', 'pass', 'schema', MySqlDbEscaper::DEFAULT_PORT
            );

        $mocked_link
            ->expects($this->never())
            ->method('set_charset');

        $mocked_link->connect_error = 'some error when connecting';
        $mocked_link->connect_errno = 500;

        try {
            $object->connect();
        } catch (Exception $e) {
            $this->assertInstanceOf(__NAMESPACE__ . '\DbLinkException', $e);
            $this->assertSame('some error when connecting', $e->getMessage());
            $this->assertSame(500, $e->getCode());
        }

        $this->assertAttributeSame(false, 'is_connected', $object);
        $this->assertAttributeCount(1, 'errors', $object);
    }

    /** @test */
    public function closeMethod()
    {
        $object = $this->getFixture(array('close'));

        $this->getMockedMysqliObject($object)
            ->expects($this->once())
            ->method('close');

        $object->close();
    }

    /** @test */
    public function realEscapeMethod()
    {
        $object = $this->getFixture(array('real_escape_string'));

        $this->getMockedMysqliObject($object)
            ->expects($this->once())
            ->method('real_escape_string')
            ->with($this->equalTo('string to be escaped'))
            ->will($this->returnArgument(0));

        $this->assertSame(
            'string to be escaped',
            $object->realEscape('string to be escaped')
        );
    }

    /** @test */
    public function magicCallMethodWhenCallable()
    {
        $object = $this->getFixture(array('magic_method'));

        $this->getMockedMysqliObject($object)
            ->expects($this->once())
            ->method('magic_method')
            ->with(
                'first_argument',
                'second_argument'
            )
            ->will($this->returnValue('some value'));

        $this->assertSame(
            'some value',
            $object->magic_method('first_argument', 'second_argument')
        );
    }

    /** @test */
    public function magicCallMethodWhenNotCallable()
    {
        $object = $this->getFixture();

        $value = $object->magic_method();

        $this->assertNull($value);
        $this->assertAttributeCount(1, 'errors', $object);
    }

    /** @test */
    public function queryMethodWhenOkAndNonMysqlResult()
    {
        $object = $this->getFixture(array('query'));

        $this->getMockedMysqliObject($object)
            ->expects($this->once())
            ->method('query')
            ->with($this->equalTo('query to launch'))
            ->will($this->returnValue(true));

        $this->assertTrue($object->query('query to launch'));
    }

    /** @test */
    public function queryMethodWhenOkAndMysqlResult()
    {
        $object = $this->getFixture(array('query'));

        $mocked_mysqli_result = $this->getMockBuilder('\mysqli_result')
            ->disableOriginalConstructor()
            ->getMock();

        $this->getMockedMysqliObject($object)
            ->expects($this->once())
            ->method('query')
            ->with($this->equalTo('query to launch'))
            ->will($this->returnValue($mocked_mysqli_result));

        $this->assertInstanceOf(
            'NeoParla\DbEscaper\Result\MysqlIterator',
            $object->query('query to launch')
        );
    }

    /** @test */
    public function queryMethodWhenKo()
    {
        $object = $this->getFixture(array('query'));

        $mocked_link = $this->getMockedMysqliObject($object);
        $mocked_link->error = 'someething goes wrong';

        $mocked_link
            ->expects($this->once())
            ->method('query')
            ->with($this->equalTo('query to launch'))
            ->will($this->returnValue(false));

        $this->setExpectedException(__NAMESPACE__ . '\DbLinkException', 'Message error: someething goes wrong');
        $object->query('query to launch');
    }

    /**
     * @return array
     */
    private function getStubbedConnectionData()
    {
        $stubbed_connection_data = array(
            'host' => 'host',
            'user' => 'user',
            'pass' => 'pass',
            'schema' => 'schema'
        );
        ksort($stubbed_connection_data);
        return $stubbed_connection_data;
    }

    /**
     * @param $methods
     * @return MySqlDbEscaper
     */
    private function getFixture(array $methods = array())
    {
        $mock = $this->getMock('\stdClass', $methods);

        $object = new MySqlDbEscaper();
        $object->setConnectionData($this->getStubbedConnectionData());

        $property = new \ReflectionProperty($object, 'link');
        $property->setAccessible(true);
        $property->setValue($object, $mock);
        unset($property);
        unset($mock);

        return $object;
    }

    /**
     * @param $object
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedMysqliObject($object)
    {
        $mocked_link = $this->readAttribute($object, 'link');
        return $mocked_link;
    }
}