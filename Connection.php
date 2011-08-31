<?php
/**
 *
 * @package     OpenFlame Dbal
 * @copyright   (c) 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Dbal;

use \PDO;
use \PDOException;
use \LogicException;
use \RuntimeException;

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
	 * Our connection object
	 * @var instance of PDO
	 */
	private $pdo = NULL;

	/*
	 * Driver Name
	 * @var string
	 */
	private $driver = '';

	/*
	 * Default connection name
	 */
	const DEFAULT_CON_NAME = 'default';

	/*
	 * Static constructor
	 * @param string $name - Name of the connection, or empty if using the default 
	 * @return \OpenFlame\Dbal\Connection - Specific instance of this class specified by the $name param  
	 */
	public static function getInstance($name = '')
	{
		$name = empty($name) ? static::DEFAULT_CON_NAME : '_' . (string) $name;

		if (!isset(static::$connections[$name]))
		{
			static::$connections[$name] = new static();
		}

		return static::$connections[$name];
	}

	/*
	 * Connect
	 * @param object instanceof PDO
	 * -- OR --
	 * @param string dsn - Connection string 
	 * @param string username - User used to connect to the DB
	 * @param string password - Password for the user
	 * @param array options - Driver-specific options
	 */
	public function connect()
	{
		$args = func_get_args();

		if ($args[0] instanceof PDO)
		{
			$this->pdo = $args[0];
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		else if (isset($args[0]))
		{
			$dsn = $args[0];
			$user = isset($args[1]) ? $args[1] : '';
			$pass = isset($args[2]) ? $args[2] : '';
			$options = isset($args[3]) ? (array) $args[3] : array();

			// Doing this before the connection, otherwise it will sit there 
			// and hang if we have bad login details.
			$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

			try
			{
				$this->pdo = new PDO($dsn, $user, $pass, $options);
			}
			catch (PDOException $e)
			{
				throw new RuntimeException("Connection failed: " . $e->getMessage());
			}
		}
		else
		{
			throw new LogicException('\OpenFlame\Dbal\Connection::connect() was not given correct parameters');
		}

		$this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
		if (!class_exists("\\OpenFlame\\Dbal\\DBMS\\{$this->driver}"))
		{
			throw new LogicException("Unsupported PDO driver: {$this->driver}");
		}
	}

	/*
	 * Get the database management system type
	 * @return string - The database management system string:
	 * 	mysql, mysqli, sqlite, pgsql, oracle, mssql
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/*
	 * Get the PDO instance
	 * @return - Instance of \PDO
	 * @throws \RuntimeException
	 */
	public function get()
	{
		if ($this->pdo == NULL)
		{
			throw new RuntimeException('Could not get PDO object from \OpenFlame\Dbal\Connection, object was NULL');
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
		if ($this->pdo == NULL)
		{
			throw new RuntimeException('Could not close database connection, object was NULL already');
		}

		$this->pdo = NULL;
	}
}
