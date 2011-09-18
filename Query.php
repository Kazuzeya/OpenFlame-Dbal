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
use \LogicException;
use \RuntimeException;

/**
 * OpenFlame Dbal - Query,
 * 	     Wraps around PDO to create an interface to query the database.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Query
{
	/**
	 * Connection Object
	 * @var instance of PDO 
	 */
	protected $pdo = NULL;

	/**
	 * Statement for our query 
	 * @var PDOStatement 
	 */
	protected $statement = NULL;

	/**
	 * SQL query 
	 * @var string 
	 */
	protected $sql = '';

	/*
	 * Limit (Default is -1, not doing limits)
	 * @var limit
	 */
	protected $limit = -1;

	/*
	 * Offset
	 * @var int
	 */
	protected $offset = 0;

	/*
	 * Parameters
	 * @var array
	 */
	protected $params = array();

	/**
	 * Statically create an instance
	 * @param string $name - Connection name
	 * @return new \OpenFlame\Dbal\Query
	 */
	public static function newInstance($name = '')
	{
		$class = '\\OpenFlame\\Dbal\\DBMS\\' . Connection::getInstance($name)->getDriver();

		return new $class($name);
	}

	/**
	 * Normal constructor
	 * @param string $name - Connection name
	 */
	public function __construct($name = '')
	{
		$this->pdo = Connection::getInstance($name)->get();
	}

	/**
	 * Set the SQL to be ran
	 * @param string $sql 
	 * @return \OpenFlame\Dbal\Query - provides a fluent interface 
	 */
	public function sql($sql)
	{
		$this->sql = trim((string) $sql);

		return $this;
	}

	/**
	 * Set the limit to the query
	 * This is not mean to be in query builder as this is a database 
	 * abstraction layer. It should provide full abstraction at this level.
	 *
	 * @param int limit
	 * @return \OpenFlame\Dbal\Query - provides a fluent interface 
	 */
	public function limit($limit)
	{
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Offset the result set
	 * @param int offset 
	 * @return \OpenFlame\Dbal\Query - provides a fluent interface 
	 */
	public function offset($offset)
	{
		$this->offset = (int) $offset;

		return $this;
	}

	/**
	 * Set params
	 * @param array parameters
	 * @return \OpenFlame\Dbal\Query - provides a fluent interface 
	 */
	public function setParams($params)
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
		if ($this->statement == NULL)
		{
			$this->query();
		}

		return $this->statement->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Query and fetch the rowset
	 * @param string indexedBy - Optionally index your rowset by a column (like
	 *	an ID). It will cast it to an int if it matches ctype_digit.
	 * @return array - Multi-dimensional associative array of the rowset being 
	 *	fetched
	 */
	public function fetchRowset($indexedBy = '')
	{
		$this->query();
		$result = $this->statement->fetchAll(PDO::FETCH_ASSOC);

		if (!empty($indexedBy) && isset($result[0][$indexedBy]))
		{
			$newResult = array();

			foreach($result as $rec)
			{
				$key = ctype_digit($rec[$indexedBy]) ? (int) $rec[$indexedBy] : $rec[$indexedBy];
				$newResult[$key] = $rec;
			}

			return $newResult;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * Excecute a query
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
	 */
	public function exec()
	{
		$this->query();

		return $this;
	}

	/**
	 * Get Row Count
	 * @return int - Number of rows affected
	 */
	public function getRowCount()
	{
		return $this->statement->rowCount();
	}

	/*
	 * Get the last insert id
	 * @return string - Insert ID
	 */
	public function insertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * Excecute a query (internally)
	 * @throws PDOException
	 */
	private function query()
	{
		if ($this->limit > 0)
		{
			$this->sql .= "\nLIMIT {$this->limit}\nOFFSET {$this->offset}";
		}

		$this->statement = $this->pdo->prepare($this->sql);
		$this->statement->execute($this->params);
	}
}
