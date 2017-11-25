<?php

namespace NeoParla\DbEscaper\Statement;

use NeoParla\DbEscaper\Statement\Binding\Binding;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class DbStatementTest extends PHPUnit_Framework_TestCase {
    const DEFAULT_QUERY = 'query';
    const DEFAULT_LABEL = 'Label';
    const DEFAULT_BINDING = Binding::STRING;

    /**
     * @var DbStatement
     */
    private $statement;

    private function randomString() {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, rand(1,1000));
    }

    /**
     * @test
     */
    public function constructorParameters() {
        $link = $this->getLinkMocked();
        $query = $this->randomString();
        $label = $this->randomString();

        $obj = new DbStatement($link, $query, $label);

        $this->assertAttributeSame($link, 'link', $obj);
        $this->assertAttributeSame($query, 'query', $obj);
        $this->assertAttributeSame($label, 'label', $obj);
    }

    /**
     * @test
     */
    public function gettingLastError() {
        $link = $this->getLinkMocked();
        $mocked_value = rand(0, 100);
        $link
            ->expects($this->once())
            ->method('getLastError')
            ->will($this->returnValue($mocked_value))
        ;

        $obj = new DbStatement($link, self::DEFAULT_QUERY, self::DEFAULT_QUERY);

        $this->assertSame($mocked_value, $obj->getError(), 'Getting last error should return the same as Link::getLastError');
    }

    /**
     * @test
     */
    public function bindingParamWhenInvalidType() {

        $this->setExpectedException(__NAMESPACE__ . '\\Binding\\BindingException', 'Invalid binding type "InvalidType"');

        $obj = new DbStatement($this->getLinkMocked(), self::DEFAULT_QUERY, self::DEFAULT_LABEL);
        $value = self::DEFAULT_QUERY;
        $obj->bindParam(self::DEFAULT_QUERY, $value, 'InvalidType');
    }

    /**
     * @test
     */
    public function bindingParameterWhenValidType() {
        $obj = new DbStatement($this->getLinkMocked(), self::DEFAULT_QUERY, self::DEFAULT_LABEL);
        $search = $this->randomString();
        $value = 'value';

        $returned = $obj->bindParam($search, $value, self::DEFAULT_BINDING);

        $parameters = $this->readAttribute($obj, 'parameters');

        $this->assertCount(1, $parameters, 'If only one bindParam called, only one parameter');
        $this->assertEquals(array('search', 'binding'), array_keys($parameters[$search]), 'Each parameter MUST have both search and binding keys defined');
        $this->assertEquals($search, $parameters[$search]['search'], 'Parameter search value must be as defined on method call');
        $this->assertInstanceOf(__NAMESPACE__ . '\\Binding\\Binding', $parameters[$search]['binding']);
        $this->assertSame($returned, $obj, 'Fluent interface required');
    }

    /**
     * @test
     */
    public function bindingMultipleParameters() {
        $obj = new DbStatement($this->getLinkMocked(), self::DEFAULT_QUERY, self::DEFAULT_LABEL);

        $value = 'value';
        // Forcing invalid bindings, will be overwritten.
        $obj->bindParam('to_replace', $value, self::DEFAULT_BINDING);
        $obj->bindParam('to_replace_2', $value, self::DEFAULT_BINDING);
        $obj->bindParam('to_replace_3', $value, self::DEFAULT_BINDING);

        $parameters = $this->readAttribute($obj, 'parameters');
        $this->assertCount(3, $parameters, 'If more than one binding, more than one parameter');
    }

    /**
     * @test
     */
    public function bindingMultipleTimesSameParameter() {
        $obj = new DbStatement($this->getLinkMocked(), self::DEFAULT_QUERY, self::DEFAULT_LABEL);

        $value = 'value';
        // Forcing invalid bindings, will be overwritten.
        $obj->bindParam('to_replace', $value, self::DEFAULT_BINDING);
        $parameters = $this->readAttribute($obj, 'parameters');
        $value_binded_first_call = $this->readAttribute($parameters['to_replace']['binding'], 'value');
        $obj->bindParam('to_replace', $value, self::DEFAULT_BINDING);
        $value = 'different_value';
        $obj->bindParam('to_replace', $value, self::DEFAULT_BINDING);

        $parameters = $this->readAttribute($obj, 'parameters');
        $value_binded_last_call = $this->readAttribute($parameters['to_replace']['binding'], 'value');


        $this->assertNotSame($value, $value_binded_first_call);
        $this->assertSame($value, $value_binded_last_call);
        $this->assertCount(1, $parameters, 'If more than one binding, more than one parameter');
    }

    /**
     * @test
     */
    public function executeWhenNoBindings()
    {
        $mocked_link = $this->getLinkMocked();

        $mocked_link
            ->expects($this->once())
            ->method('connect');

        $query = $this->randomString();
        $mocked_link
            ->expects($this->once())
            ->method('query')
            ->with($this->identicalTo($query))
            ->will($this->returnValue($query));

        $obj = new DbStatement($mocked_link, $query, $this->randomString());

        $this->assertEquals(
            $query,
            $obj->execute(),
            'When executing, method should return directly from Link::execute() method'
        );
    }

    /**
     * @test
     */
    public function executeWhenBindingsOnQuery()
    {
        $query = 'query to be :parsed';

        $mocked_link = $this->getLinkMocked();

        $mocked_link
            ->expects($this->once())
            ->method('connect');

        $mocked_link
            ->expects($this->once())
            ->method('query')
            ->with(
                $this->equalTo('query to be \'executed\'')
            )
            ->will($this->returnValue($query));

        $mocked_link
            ->expects($this->once())
            ->method('realEscape')
            ->will($this->returnArgument(0));

        $obj = new DbStatement($mocked_link, $query, $this->randomString());

        $value = 'executed';
        $obj->bindParam(':parsed', $value, Binding::STRING);

        $this->assertEquals(
            $query,
            $obj->execute(),
            'When executing, method should return directly from Link::execute() method'
        );
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getLinkMocked()
    {
        $link = $this->getMockForAbstractClass('NeoParla\\DbEscaper\\Link');
        return $link;
    }
} 