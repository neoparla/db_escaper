<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use stdClass;

class DoubleBinderTest extends BindingTestAbstract {

    /**
     * @return string
     */
    protected function getBindingType()
    {
        return Binding::DOUBLE;
    }

    /**
     * Invalid types.
     */
    public function invalidTypesProvider()
    {
        return array(
            'Boolean value' => array(true),
            'String value' => array('some string'),
            'Object value' => array(new StdClass),
        );
    }

    public function validTypesProvider() {
        return array(
            'Double' => array(1.2),
            'Integer' => array(1),
            'Equivalent to Double' => array('1.2'),
        );
    }

    /**
     * @param $value
     * @return double
     */
    protected function expectedRealValue($value)
    {
        return doubleval($value);
    }
}