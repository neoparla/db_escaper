<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 4/9/15
 * Time: 7:55 PM
 */
namespace NeoParla\DbEscaper;

use PHPUnit_Framework_TestCase;

class LinkTest extends PHPUnit_Framework_TestCase
{

    /** @test */
    public function methods()
    {
        $needed_methods = array(
            'connect',
            'query',
            'close',
            'realEscape',
            '__call',
            'setConnectionData',
            'getLastError',
            'getShellCommand'
        );

        $this->assertThat(
            array_unique(get_class_methods(__NAMESPACE__ . '\Link')),
            new \PHPUnit_Framework_Constraint_Callback(function ($class_methods) use ($needed_methods) {
                return $needed_methods === array_intersect($needed_methods, $class_methods);
            })
        );
    }
} 