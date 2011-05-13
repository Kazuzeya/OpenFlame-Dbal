<?php
/**
 *
 * @package     OpenFlame Dbal
 * @copyright   (c) 2011 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Dbal;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Dbal - Connection
 * 	     Static class to connect and manage PDO instances for the querier
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Connection
{
	/*
	 * @var Instance of PDO
	 */
	protected static $pdo = array();

	/*
	 * Statically get the PDO instance
	 * @param int - connection ID (Defaults to first connection)
	 * @return Connection instance
	 */
	public static function get($i = 0)
	{
		if(!isset(static::$pdo[$i]))
		{
			// @todo
			//throw new Exception();
		}

		return static::$pdo[$i];
	}

	/*
	 * Connect to a database
	 *
	 * OPTION 1:
	 ***************************
	 * @param PDO instance
	 *
	 * OPTION 2:
	 ***************************
	 * @param string - DSN connection string 
	 * @param string - Database username
	 * @param string - Database password
	 * @param array - DBMS specific options
	 *
	 * @return mixed - int on success, boolean false on failure 
	 */
	public static function connect()
	{
		$args = func_get_args();
		$i = sizeof(static::$pdo);

		// Shortcut method
		if($args[0] instanceof PDO)
		{
			static::$pdo[$i] = $args[0];
			return $i; 
		}

		if(empty($args[0]))
		{
			// @todo
			//throw new Exception();
		}

		// If argument 1 is not an instance of PDO, we're connecting normally
		$dsn		= $args[0];
		$username	= isset($args[1]) ? $args[1] : '';
		$password	= isset($args[2]) ? $args[2] : '';
		$options	= isset($args[3]) && is_array($args[3]) ? $args[3] : array();

		try 
		{
			static::$pdo[$i] = new \PDO($dsn, $username, $password, $options);
		}
		catch(PDOException $e)
		{
			// @todo
			return false; 
		}

		return $i; 
	}
}
