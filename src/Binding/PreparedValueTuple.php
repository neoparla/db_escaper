<?php
/**
 * Created by PhpStorm.
 * User: Pau
 * Date: 21/11/13
 * Time: 23:55
 */

namespace NeoParla\DbEscaper;

class PreparedValueTuple implements PreparedValue
{

	/**
	 * @var DbTuple
	 */
	protected $value;

	public function __construct( &$value )
	{
		$this->value = $value;
	}

	public function isValid()
	{
		return $this->value instanceof DbTuple;
	}

	public function getValue( DbLink $link )
	{
		if ( !$this->isValid() )
		{
			throw new PreparedValue_Exception( 'Not a valid DbTuple' );
		}

		return $this->value->getValue( $link );
	}

}