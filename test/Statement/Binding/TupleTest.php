<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use PHPUnit_Framework_MockObject_MockObject;
use stdClass;

class TupleTest extends BindingTestAbstract {

    /**
     * @return string
     */
    protected function getBindingType()
    {
        return Binding::TUPLE;
    }

    /**
     * Invalid types.
     */
    public function invalidTypesProvider()
    {
        return array(
            'Double value' => array(1.2),
            'Boolean value' => array(true),
            'String value' => array('some string'),
            'Object value' => array(new StdClass),
        );
    }

    public function validTypesProvider() {
        return array(
            'Tuple definition' => array(
                $this->getMockBuilder('NeoParla\DbEscaper\Statement\DbTuple')
                    ->disableOriginalConstructor()
                    ->setMethods(array('buildValues'))
                    ->getMock()
            ),
        );
    }

    /**
     * @test
     * @dataProvider validTypesProvider
     */
    public function gettingRealValueWhenValidTypes($value)
    {
        if (!$value instanceof PHPUnit_Framework_MockObject_MockObject) {
            $this->fail('Invalid type value');
        }

        $value
            ->expects($this->once())
            ->method('buildValues')
            ->will($this->returnValue('value returned from buildValues'));

        parent::gettingRealValueWhenValidTypes($value);
    }


    /**
     * @param $value
     * @return integer
     */
    protected function expectedRealValue($value)
    {
        return 'value returned from buildValues';
    }
}