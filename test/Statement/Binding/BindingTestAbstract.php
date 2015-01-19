<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 1/19/15
 * Time: 10:25 AM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use NeoParla\DbEscaper\Link;
use PHPUnit_Framework_TestCase;
use stdClass;

abstract class BindingTestAbstract extends PHPUnit_Framework_TestCase{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $link;

    /**
     * @return Binding
     */
    protected function getFixture($fixture_type, $value) {
        $class = __NAMESPACE__ . '\\' . $fixture_type;
        if (!class_exists($class)) {
            $this->fail("Not a valid type");
        }

        return new $class($this->getMockedLink(), $value);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockedLink()
    {
        $this->link = $this->getMockForAbstractClass('NeoParla\DbEscaper\Link');

        return $this->link;
    }

    /**
     * @return string
     */
    abstract protected function getBindingType();

    /**
     * Invalid types.
     */
    abstract public function invalidTypesProvider();

    abstract public function validTypesProvider();

    /**
     * @test
     * @dataProvider validTypesProvider
     */
    final public function validTypes($value) {
        $type = $this->getBindingType();
        $this->assertTrue(
            $this->getFixture($type, $value)->isValid(),
            $type . '::isValid() should be TRUE only if provided binding is a valid' . $type
        );
    }

    /**
     * @test
     * @dataProvider invalidTypesProvider
     */
    final public function inValidTypes($value) {
        $type = $this->getBindingType();
        $this->assertFalse(
            $this->getFixture($type, $value)->isValid(),
            $type . '::isValid() should be FALSE if provided binding is NOT a valid ' . $type
        );
    }

    /**
     * @test
     * @dataProvider validTypesProvider
     */
    final public function gettingRealValueWhenValidTypes($value) {
        $fixture = $this->getFixture($this->getBindingType(), $value);

        $this->assertEquals(
            $this->expectedRealValue($value),
            $fixture->getRealValue()
        );
    }

    /**
     * @test
     * @dataProvider inValidTypesProvider
     */
    final public function gettingRealValuesWhenInvalidTypes($value) {
        $this->setExpectedException(__NAMESPACE__ . '\\BindingException');

        $this->getFixture($this->getBindingType(), $value)->getRealValue();
    }

    /**
     * @param $value
     * @return string
     */
    abstract protected function expectedRealValue($value);

} 