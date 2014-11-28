<?php
/**
 * @author Pau Perez <pau.perez.molins@gmail.com>
 */

namespace NeoParla\DbEscaper;

use JenFrame\Core\AppConfig;

class Database
{

	protected $statements = array();
	private static $instance = array();

	/**
	 * @var DbLink
	 */
	protected $db_link;

	private function __construct( $profile, AppConfig $config )
	{
		$this->db_link = new DbLink( $config->getConfig( 'Db', $profile ) );
	}

	/**
	 * @param $profile
	 * @param AppConfig $config
	 *
	 * @return Database
	 */
	public static function getInstance( $profile, AppConfig $config )
	{
		if ( !isset( self::$instance[$profile] ) )
		{
			self::$instance[$profile] = new self( $profile, $config );
		}

		return self::$instance[$profile];
	}

	public function getPreparedStatement( $sql, $label )
	{
		$statement = new DbStatement( $sql, $this->db_link );

		$this->statements[] = array(
			'label'		=> $label,
			'statement'	=> $statement
		);

		return $statement;
	}

}

class Database_Exception extends \Exception {}