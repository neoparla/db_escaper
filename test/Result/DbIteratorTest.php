<?php

namespace NeoParla\DbEscaper\Result;

use PHPUnit_Framework_TestCase;

class DbIteratorTest extends PHPUnit_Framework_TestCase
{

    /** @test */
    public function correctInheritance()
    {
        $class_implements = class_implements(__NAMESPACE__ . '\DbIterator');
        sort($class_implements);
        $this->assertSame(
            array(
                "ArrayAccess",
                "Countable",
                "Iterator",
                "Traversable"
            ),
            $class_implements
        );
    }

    /** @test */
    public function methods()
    {
        $needed_methods = array(
            'toArray'
        );

        $this->assertThat(
            array_unique(get_class_methods(__NAMESPACE__ . '\DbIterator')),
            new \PHPUnit_Framework_Constraint_Callback(function ($class_methods) use ($needed_methods) {
                return $needed_methods === array_intersect($needed_methods, $class_methods);
            })
        );
    }
} 