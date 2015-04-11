<?php

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