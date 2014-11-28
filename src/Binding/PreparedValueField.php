<?php
/**
 * Created by PhpStorm.
 * User: Pau
 * Date: 22/11/13
 * Time: 0:26
 */

namespace NeoParla\DbEscaper;

class PreparedValueField implements PreparedValue
{

	protected $value;
	public function __construct( &$value )
	{
		$this->value = $value;
	}

	public function isValid()
	{
		return is_string( $this->value );
	}

	public function getValue( DbLink $link )
	{
		if ( !$this->isValid() )
		{
			throw new PreparedValue_Exception( $this->value . ' is not a valid field name' );
		}

		return '`' . $this->value . '`';
	}

}