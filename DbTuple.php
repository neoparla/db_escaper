<?php
/**
 * Created by PhpStorm.
 * User: Pau
 * Date: 21/11/13
 * Time: 23:46
 */

namespace JenFrame\Core\Db;

class DbTuple
{
	const WITH_PARENTHESIS		= true;
	const WITHOUT_PARENTHESIS	= false;

	protected $types;
	protected $values;
	protected $parenthesis;

	public function __construct( $types, array $values, $with_parenthesis )
	{
		$this->values		= $values;
		$this->parenthesis	= $with_parenthesis;

		if ( is_array( $types ) && count( $types ) !== count( $values ) )
		{
			throw new DbStatement_Exception( 'Number of types and parameters doesn\'t match' );
		}

		$this->types = is_array( $types ) ? $types : array_fill( 0, count( $values ), $types );
	}

	public function getValue( DbLink $link )
	{
		$count	= 0;
		$max	= count( $this->types );

		$real_values = array();
		for( ; $count < $max; $count++ )
		{
			$parser = '\JenFrame\Core\Db\PreparedValue' . $this->types[$count];
			$parser = new $parser( $this->values[$count] );
			$real_values[] = $parser->getValue( $link );
		}

		$real_values = implode( ', ', $real_values );
		if ( $this->parenthesis )
		{
			$real_values = '( ' . $real_values . ' )';
		}

		return $real_values;
	}
}