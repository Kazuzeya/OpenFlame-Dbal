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
	protected $stmt;

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

		return $this->stmt->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Query and fetch the rowset
	 * @return array - Multi-dimensional associative array of the rowset being fetched
	 */
	public function fetchRowset()
	{
		$this->_query();

		return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
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
	 * Get the last insert id
	 * @return string - Insert ID
	 */
	public function insertId()
	{
		return $this->pdo->lastInsertId();
	}

	/*
	 * Excecute a query (internally)
	 * @param bool $hard - Run it even if a query has been ran for this instance.
	 * @throws \LogicException
	 */
	protected function _query($hard = false)
	{
		static $queryRan = false;

		if (!$queryRan || $hard)
		{
			$this->stmt = $this->pdo->prepare($this->sql);
			$this->stmt->execute($this->params);

			$queryRan = true;

			list($e, $c, $m) = $this->stmt->errorInfo();

			if ($c || strlen($m))
			{
				throw new \LogicException("SQL Error: {$e}, Code: {$c}. {$m}");
			}
		}
	}
}
