<?php
/**
 * Created by PhpStorm.
 * User: Pau
 * Date: 21/11/13
 * Time: 21:17
 */

namespace NeoParla\DbEscaper;

class DbStatement
{
	const PARAM_STRING = 'String';
	const PARAM_INTEGER = 'Integer';
	const PARAM_TUPLE = 'Tuple';
	const PARAM_FIELD = 'Field';

	protected $query;
	protected $error;
	protected $result;

	protected $params = array();

	/**
	 * @var DbLink
	 */
	protected $db_link;

	public function __construct( $sql, DbLink $db_link )
	{
		$this->query	= $sql;
		$this->db_link	= $db_link;
	}

	public function bindParam( $param, &$value, $type )
	{
		$this->params[$param] = $this->getParamPair( $param, $value, $type );
	}

	protected function getParamPair( $param, $value, $type )
	{
		if ( !$this->isValidParamType( $type ) )
		{
			throw new DbStatement_Exception( $type . ' is not a valid param type' );
		}

		$class = 'JenFrame\Core\Db\PreparedValue' . $type;

		return array(
			'param'		=> $param,
			'parser'	=> new $class( $value )
		);
	}

	public function createTuple( $types, $values, $with_parenthesis = DbTuple::WITH_PARENTHESIS )
	{
		return new DbTuple( $types, $values, $with_parenthesis );
	}

	public function isValidParamType( $type )
	{
		return (
			self::PARAM_STRING === $type
			|| self::PARAM_INTEGER === $type
			|| self::PARAM_TUPLE === $type
		);
	}

	public function execute()
	{
		$this->db_link->connect();

		$query = $this->replaceParams();
//		echo '<pre>' . $query . '</pre>';die;
		return $this->run( $query );
	}

	protected function replaceParams()
	{
		$query = $this->query;
		foreach ( $this->params as $param )
		{
			$real_value = $param['parser']->getValue( $this->db_link );

			$query = str_replace( $param['param'], $real_value, $query );
		}

		return $query;
	}

	protected function run( $query )
	{
		$this->db_link->connect();

		try
		{
			return $this->db_link->query( $query );
		}
		catch ( DbLink_Exception $e )
		{
			$this->error = $e->getMessage();
			return false;
		}
	}

	public function getLastError()
	{
		return $this->error;
	}
}

class DbStatement_Exception extends Database_Exception {}