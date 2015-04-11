<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use stdClass;

class StringTest extends BindingTestAbstract {

    /**
     * @return string
     */
    protected function getBindingType()
    {
        return Binding::STRING;
    }

    /**
     * Invalid types.
     */
    public function invalidTypesProvider()
    {
        return array(
            'Invalid - Numeric value' => array(1),
            'Invalid - Double value' => array(1.2),
            'Invalid - Boolean value' => array(true),
            'Invalid - Object value' => array(new StdClass),
        );
    }

    public function validTypesProvider() {
        return array(
            'Valid - Single character' => array('c', true),
            'Valid - Singe word' => array('Word', true),
            'Valid - Multiple words' => array('This is a valid string', true),
        );
    }

    /**
     * @param $value
     * @return string
     */
    protected function expectedRealValue($value)
    {
        $this->link
        ->expects($this->once())
        ->method('realEscape')
        ->with($this->equalTo($value))
        ->will($this->returnArgument(0));

        return "'$value'";
    }
}