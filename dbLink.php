<?php
/**
 * @author Pau Perez <pau.perez.molins@gmail.com>
 */

namespace JenFrame\Core\Db;

class DbLink
{
	protected $host;
	protected $user;
	protected $pass;
	protected $schema;

	protected $port = 3306;

	/**
	 * @var \Mysqli
	 */
	protected $mysqli_link;
	public function __construct( array $configuration )
	{
		$this->host = $configuration['host'];
		$this->user = $configuration['user'];
		$this->pass = $configuration['pass'];

		$this->schema = $configuration['schema'];

		if ( isset( $configuration['port'] ) )
		{
			$this->port = $configuration['port'];
		}
	}

	public function connect()
	{
		if ( null === $this->mysqli_link )
		{
			$this->mysqli_link = new \mysqli();
			@$this->mysqli_link->connect( $this->host, $this->user, $this->pass, $this->schema, $this->port );

			if ( null !== $this->mysqli_link->connect_error )
			{
				throw new DbLink_Exception( $this->mysqli_link->error );
			}

			$this->mysqli_link->query( 'SET NAMES UTF8' );
		}
	}

	public function query( $sql )
	{
		$result_iterator = $this->mysqli_link->query( $sql, MYSQLI_ASSOC );
		if ( !$result_iterator )
		{
			$message = <<<ERROR
Error on '{$this->user}@{$this->host}' when executing
{$sql}

Message error: {$this->mysqli_link->error}
ERROR;

			throw new DbLink_Exception( $message );
		}

		if ( !$result_iterator instanceof \mysqli_result )
		{
			return $result_iterator;
		}

		$data = array();
		while ( $raw = $result_iterator->fetch_assoc() )
		{
			$data[] = $raw;
		}

		return $data;
	}

	public function escapeString( $string )
	{
		return $this->mysqli_link->escape_string( $string );
	}

	public function __call( $method, $params )
	{
		if ( is_callable( array( $this->mysqli_link, $method ) ) )
		{
			return call_user_func_array( array( $this->mysqli_link, $method ), $params );
		}
	}

}

class DbLink_Exception extends Database_Exception {}