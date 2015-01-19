<?php

namespace NeoParla\DbEscaper\Statement;


use NeoParla\DbEscaper\Link;
use NeoParla\DbEscaper\Statement\Binding\BindingException;

class DbTuple {

    const WITH_PARENTHESIS		= true;
    const WITHOUT_PARENTHESIS	= false;

    protected $types;
    protected $values;
    protected $parenthesis;

    public function __construct( $types, array $values, $with_parenthesis )
    {
        $this->values		= $values;
        $this->parenthesis	= $with_parenthesis;
        $this->types        = $types;
    }

    /**
     * @param Link $link
     * @return string
     * @throws Binding\BindingException
     */
    public function buildValues( Link $link )
    {
        $this->buildTypesArray();

        $count	= 0;
        $max	= count( $this->types );

        $values = array();
        for( ; $count < $max; $count++ )
        {
            $class = __NAMESPACE__ . '\\Binding\\' . $this->types[$count];
            if (!class_exists($class)) {
                throw new BindingException('Invalid binding type "' . $this->types[$count] . '"');
            }
            $class = new $class( $link, $this->values[$count] );
            $values[] = $class->getRealValue();
        }

        $real_values = implode( ', ', $values );
        if ( $this->parenthesis )
        {
            $real_values = '(' . $real_values . ')';
        }

        return $real_values;
    }

    /**
     * @return array
     * @throws Binding\BindingException
     */
    private function buildTypesArray()
    {
        if (is_array($this->types) && count($this->types) !== count($this->values)) {
            throw new BindingException('Number of types and parameters doesn\'t match');
        }

        $this->types = is_array($this->types) ? $this->types : array_fill(0, count($this->values), $this->types);
    }
}