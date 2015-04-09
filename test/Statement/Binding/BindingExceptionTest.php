<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 4/9/15
 * Time: 7:27 PM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use PHPUnit_Framework_TestCase;

class BindingExceptionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function correctInheritance()
    {
        $this->assertInstanceOf(
            'NeoParla\DbEscaper\DbEscaperException',
            new BindingException()
        );
    }
} 