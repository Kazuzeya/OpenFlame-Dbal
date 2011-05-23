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

if (!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Dbal - Query
 * 	     Wraps around PDO to create an interface to query the database
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Query
{
	/*
	 * @var PDO connection
	 */
	private $pdo;

	/*
	 * @var PDO Statement
	 */
	private $stmt;

	/*
	 * @var SQL to be ran
	 */
	protected $sql = '';

	/*
	 * @var Statement params to be bound
	 */
	protected $params = array();

	/*
	 * Statically get an instance
	 * @param string $name - Connection name
	 * @return new \OpenFlame\Dbal\Query
	 */
	public static function getInstance($name = '')
	{
		return new static($name);
	}

	/*
	 * Normal constructor
	 * @param string $name - Connection name
	 */
	public function __construct($name = '')
	{
		$this->pdo = \OpenFlame\Dbal\Connection::getInstance($name)->get();
	}

	/*
	 * Set the SQL to be ran
	 * @param string $sql 
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
	 */
	public function sql($sql)
	{
		$this->sql = (string) $sql;

		return $this;
	}

	/*
	 * Set params
	 * @param array $params - to run through PDO's prepared statements
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
	 */
	public function setParams($params = array())
	{
		$this->params = (array) $params;

		return $this;
	}

	/**
	 * Query and fetch a row 
	 * @note - Safe for itteration
	 * @return array - The result being fetched
	 */
	public function fetchRow()
	{
		$this->_query();

		return $this->stmt->fetch(\PDO::FETCH_ASSOC & \PDO::FETCH_LAZY);
	}

	/**
	 * Query and fetch the rowset
	 * @return array - Multi-dimensional associative array of the rowset being fetched
	 */
	public function fetchRowset()
	{
		$this->_query();

		return $this->stmt->fetchAll(\PDO::FETCH_COLUMN & \PDO::FETCH_GROUP);
	}

	/**
	 * Excecute a query
	 * @return int - Number of rows affected
	 */
	public function exec()
	{
		$this->_query();

		return $this->stmt->rowCount();
	}

	/*
	 * Excecute a query (internally)
	 */
	protected function _query()
	{
		static $queryRan = false;

		if (!$queryRan)
		{
			$this->stmt = $this->pdo->prepare($this->sql);
			$this->stmt->execute($this->params);

			$queryRan = true;
		}
	}
}
