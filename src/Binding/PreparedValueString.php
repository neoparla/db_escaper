<?php
/**
 * Created by PhpStorm.
 * User: Pau
 * Date: 21/11/13
 * Time: 23:07
 */

namespace NeoParla\DbEscaper;

class PreparedValueString implements PreparedValue
{

	protected $raw_value;

	public function __construct( &$value )
	{
		$this->raw_value = $value;
	}

	public function isValid()
	{
		return is_string( $this->raw_value );
	}

	public function getValue( DbLink $db_link )
	{
		if ( !$this->isValid() )
		{
			throw new PreparedValue_Exception( '"' . $this->raw_value . '" is not a valid string' );
		}

		return '\'' . $db_link->escapeString( $this->raw_value ) . '\'';
	}

}