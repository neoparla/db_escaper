<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use stdClass;

class IntegerBinderTest extends BindingTestAbstract {

    /**
     * @return string
     */
    protected function getBindingType()
    {
        return Binding::INTEGER;
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
            'Integer' => array(1),
            'Equivalent to Integer' => array('1'),
        );
    }

    /**
     * @param $value
     * @return integer
     */
    protected function expectedRealValue($value)
    {
        return intval($value);
    }
}