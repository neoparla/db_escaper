<?php

namespace NeoParla\DbEscaper;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;

class DbEscaperTest extends PHPUnit_Framework_TestCase
{

    /** @test */
    public function initConnectionData()
    {
        $stubbed_connection_data = $this->getStubbedConnectionData();

        $db_escaper = DbEscaper::init($stubbed_connection_data);

        $this->assertAttributeInstanceOf(__NAMESPACE__ . '\Link', 'link', $db_escaper);

        $this->assertSame($stubbed_connection_data, $db_escaper->getLink()->getConnectionData());
    }

    /** @test */
    public function singleTonImplementation()
    {
        $connection_data = $this->getStubbedConnectionData();
        $first_call = DbEscaper::init($connection_data);
        $second_call = DbEscaper::init($connection_data);

        $this->assertSame($first_call, $second_call);

        /** @var DbEscaper[] $instances */
        $instances = $this->readAttribute($first_call, 'instances');

        $this->assertCount(1, $instances);

        $instance_key = json_encode($connection_data);
        $this->assertArrayHasKey(
            $instance_key,
            $instances
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\DbEscaper', $instances[$instance_key]);
    }

    /** @test */
    public function queryMethod()
    {
        $db_escaper = DbEscaper::init($this->getStubbedConnectionData());
        $property = new ReflectionProperty($db_escaper, 'link');
        $property->setAccessible(true);
        $property->setValue($db_escaper, $this->getMockForAbstractClass(__NAMESPACE__ . '\Link'));
        unset($property);

        /** @var PHPUnit_Framework_MockObject_MockObject $mocked_link */
        $mocked_link = $this->readAttribute($db_escaper, 'link');

        $mocked_link
            ->expects($this->once())
            ->method('connect');

        $mocked_link
            ->expects($this->once())
            ->method('query')
            ->with(
                $this->equalTo('some query')
            )
            ->will($this->returnValue('some value'));

        $this->assertSame(
            'some value',
            $db_escaper->query('some query')
        );
    }

    /** @test */
    public function prepareMethod()
    {
        $this->assertInstanceOf(
            __NAMESPACE__ . '\Statement\DbStatement',
            DbEscaper::init($this->getStubbedConnectionData())->prepare('some query', 'some label')
        );
    }

    /** @test */
    public function getLinkAsProperty()
    {
        $db_escaper = DbEscaper::init($this->getStubbedConnectionData());

        $this->assertAttributeSame(
            $db_escaper->getLink(),
            'link',
            $db_escaper
        );
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
            'schema' => 'schema',
            'port' => 'port'
        );
        ksort($stubbed_connection_data);
        return $stubbed_connection_data;
    }
} 