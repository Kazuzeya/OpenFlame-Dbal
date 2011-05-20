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
	 * @var static instances of this class
	 */
	private static $connections = array();

	/*
	 * @var instance of PDO
	 */
	private $pdo = null;

	/*
	 * Default connection name
	 */
	const DEFAULT_CON_NAME = 'default';

	/*
	 * Static constructor
	 * @param string $name - Name of the connection, or empty if using the default 
	 * @return \OpenFlame\Dbal\Connection - Specific instance of this class specified by the $name param  
	 */
	public static function newInstance($name = '')
	{
		$name = empty($name) ? static::DEFAULT_CON_NAME : '_' . (string) $name;

		if (isset(static::$connections[$name]))
		{
			throw new \LogicException('Cannot overwrite existing database connection');
		}

		static::$connections[$name] = new static();
		return static::$connections[$name];
	}

	/*
	 * Get the object or creates a new object if needed
	 * @param string $name - Name of the connection, or empty if using the default
	 */
	public static function getInstance($name = '')
	{
		$name = empty($name) ? static::DEFAULT_CON_NAME : '_' . (string) $name;

		return isset(static::$connections[$name]) ? static::$connections[$name] : static::newInstance($name);
	}

	/*
	 */
	public function connect()
	{
		$args = func_get_args();

		if($args[0] instanceof \PDO)
		{
			$this->pdo = $args[0];
		}
		else if(isset($args[0]))
		{
			$dsn = (string) $args[0];
			$user = isset($args[1]) ? (string) $args[1] : '';
			$pass = isset($args[2]) ? (string) $args[2] : '';
			$options = isset($args[3]) ? (array) $args[3] : array();

			try
			{
				$this->pdo = new \PDO($dsn, $user, $pass, $options);
			}
			catch (PDOException $e)
			{
				throw new \RuntimeException($e->getMessage());
			}
		}
		else
		{
			throw new \LogicException('Connection::connect() was not given correct parameters');
		}
	}

	/*
	 * Get the PDO instance
	 * @return - Instance of \PDO
	 * @throws \RuntimeException
	 */
	public function get()
	{
		if ($this->pdo == null)
		{
			throw new \RuntimeException('Could not get PDO object from \OpenFlame\Dbal\Connection, object was NULL');
		}

		return $this->pdo;
	}

	/*
	 * Close the connection
	 * @param string $name - Name of the connection, or empty if using the default
	 * @throws \RuntimeException()
	 */
	public function close()
	{
		if ($this->pdo == null)
		{
			throw new \RuntimeException('Could not close database connection, object was NULL already');
		}

		$this->pdo = null;
	}
}
