<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 1/19/15
 * Time: 11:47 AM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use stdClass;

class IntegerTest extends BindingTestAbstract {

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