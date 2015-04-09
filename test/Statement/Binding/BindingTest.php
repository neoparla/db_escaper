<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 4/9/15
 * Time: 7:30 PM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use PHPUnit_Framework_TestCase;

class BindingTest extends PHPUnit_Framework_TestCase
{


    /** @test */
    public function methods()
    {
        $needed_methods = array(
            'isValid',
            'getRealValue'
        );
        $this->assertThat(
            array_unique(get_class_methods(__NAMESPACE__ . '\Binding')),
            new \PHPUnit_Framework_Constraint_Callback(function ($class_methods) use ($needed_methods) {
                return $needed_methods === array_intersect($needed_methods, $class_methods);
            })
        );
    }
}
 