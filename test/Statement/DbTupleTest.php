<?php

namespace NeoParla\DbEscaper\Statement;

use PHPUnit_Framework_TestCase;
use NeoParla\DbEscaper\Statement\Binding\Binding;
use NeoParla\DbEscaper\Statement\String as StringParent;

class DbTupleTest extends PHPUnit_Framework_TestCase
{

    public function validTuplesProvider()
    {
        return array(
            'Using non array types, without parenthesis' => array(
                'types' => Binding::INTEGER,
                'values' => array(1, 2),
                'parenthesis' => DbTuple::WITHOUT_PARENTHESIS,
                'expected'  => '1, 2'
            ),
            'Using an array types, without parenthesis' => array(
                'types' => array(Binding::INTEGER, Binding::STRING),
                'values' => array(1, 'String'),
                'parenthesis' => DbTuple::WITHOUT_PARENTHESIS,
                'expected'  => '1, \'String\''
            ),
            'Using non array types, with parenthesis' => array(
                'types' => Binding::INTEGER,
                'values' => array(1, 2),
                'parenthesis' => DbTuple::WITH_PARENTHESIS,
                'expected'  => '(1, 2)'
            ),
            'Using an array types, with parenthesis' => array(
                'types' => array(Binding::INTEGER, Binding::STRING),
                'values' => array(1, 'String'),
                'parenthesis' => DbTuple::WITH_PARENTHESIS,
                'expected'  => '(1, \'String\')'
            ),
        );
    }

    /**
     * @test
     * @dataProvider validTuplesProvider
     */
    public function buildValuesWhenNonArrayType($types, array $values, $with_parenthesis, $expected)
    {
        $fixture = new DbTuple(
            $types,
            $values,
            $with_parenthesis
        );

        $result = $fixture->buildValues($this->getLink());

        $this->assertEquals( $expected, $result);
    }

    /**
     * @test
     */
    public function buildValuesWhenArrayTypesButNotAsValues()
    {
        $fixture = new DbTuple(
            array(Binding::INTEGER,Binding::INTEGER),
            array(1,2,'String'),
            DbTuple::WITHOUT_PARENTHESIS
        );

        $this->setExpectedException(
            __NAMESPACE__ . '\Binding\BindingException',
            'Number of types and parameters doesn\'t match'
        );

        $fixture->buildValues($this->getMockForAbstractClass('NeoParla\DbEscaper\Link'));
    }

    /**
     * @test
     */
    public function buildValuesWhenInvalidBindingType()
    {
        $fixture = new DbTuple(
            'Invalid',
            array(1,2,'String'),
            DbTuple::WITHOUT_PARENTHESIS
        );

        $this->setExpectedException(
            __NAMESPACE__ . '\Binding\BindingException',
            'Invalid binding type "Invalid"'
        );

        $fixture->buildValues($this->getMockForAbstractClass('NeoParla\DbEscaper\Link'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getLink()
    {
        $link = $this->getMockForAbstractClass('NeoParla\DbEscaper\Link');

        $link
            ->expects($this->any())
            ->method('realEscape')
            ->will($this->returnArgument(0))
            ;
        return $link;
    }
}