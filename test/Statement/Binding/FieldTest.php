<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 1/19/15
 * Time: 11:47 AM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use stdClass;

class FieldTest extends BindingTestAbstract {

    /**
     * @return string
     */
    protected function getBindingType()
    {
        return Binding::FIELD;
    }

    /**
     * Invalid types.
     */
    public function invalidTypesProvider()
    {
        return array(
            'Double value' => array(1.2),
            'Boolean value' => array(true),
            'Object value' => array(new StdClass),
            'Fields terminated with space' => array('field ended with space '),
            'Fields with / character' => array('field/field'),
            'Fields with \ character' => array('field\field'),
            'Fields with . character' => array('field.field'),
            'Fields with ; character' => array('field;field'),
            'Fields with : character' => array('field:field'),
            'Fields with - character' => array('field-field'),
//            'Field with symbols value' => array('field:with:symbols'),
        );
    }

    public function validTypesProvider() {
        return array(
            'Single word' => array('field'),
            'Multiple words with underscore' => array('field_1'),
            'Multiple words with spaces' => array('field 1'),
        );
    }

    /**
     * @param $value
     * @return integer
     */
    protected function expectedRealValue($value)
    {
        return "`$value`";
    }
}