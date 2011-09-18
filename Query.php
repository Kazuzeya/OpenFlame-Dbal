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
	 * @var \PDO - Our connection object.
	 */
	protected $pdo = NULL;

	/**
	 * @var \PDOStatement - The statement for our query.
	 */
	protected $statement = NULL;

	/**
	 * @var string - The SQL query.
	 */
	protected $sql = '';

	/*
	 * @var $limit - The limit. Default -1, not doing limits.
	 */
	protected $limit = -1;

	/*
	 * @var int - Offset.
	 */
	protected $offset = 0;

	/*
	 * @var array - Parameters.
	 */
	protected $params = array();

	/**
	 * Statically create an instance.
	 * @param string $name - The Connection name.
	 * @return \OpenFlame\Dbal\Query - New instance of \OpenFlame\Dbal\Query.
	 */
	public static function newInstance($name = '')
	{
		$class = '\\OpenFlame\\Dbal\\DBMS\\' . Connection::getInstance($name)->getDriver();

		return new $class($name);
	}

	/**
	 * Constructor.
	 * @param string $name - The connection name.
	 */
	public function __construct($name = '')
	{
		$this->pdo = Connection::getInstance($name)->get();
	}

	/**
	 * Set the SQL to be ran
	 * @param string $sql
	 * @return \OpenFlame\Dbal\Query - Provides a fluent interface.
	 */
	public function sql($sql)
	{
		$this->sql = trim((string) $sql);

		return $this;
	}

	/**
	 * Set the limit to the query.
	 * @param int limit
	 * @return \OpenFlame\Dbal\Query - Provides a fluent interface.
	 *
	 * @note - This is not mean to be in query builder as this is a database abstraction layer. It should provide full abstraction at this level.
	 */
	public function limit($limit)
	{
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Offset the result set
	 * @param int offset 
	 * @return \OpenFlame\Dbal\Query - Provides a fluent interface.
	 */
	public function offset($offset)
	{
		$this->offset = (int) $offset;

		return $this;
	}

	/**
	 * Set paramaters.
	 * @param array - The parameters.
	 * @return \OpenFlame\Dbal\Query - Provides a fluent interface.
	 */
	public function setParams($params)
	{
		$this->params = (array) $params;

		return $this;
	}

	/**
	 * Query and fetch a row.
	 * @return array - The result being fetched.
	 *
	 * @note - Safe for itteration.
	 */
	public function fetchRow()
	{
		if($this->statement == NULL)
		{
			$this->query();
		}

		return $this->statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Query and fetch the rowset.
	 * @param string $indexedBy - Optionally index your rowset by a column (like an ID). It will cast it to an int if it matches ctype_digit.
	 * @return array - Multi-dimensional associative array of the rowset being fetched.
	 */
	public function fetchRowset($indexedBy = '')
	{
		$this->query();
		$result = $this->statement->fetchAll(\PDO::FETCH_ASSOC);

		if(!empty($indexedBy) && isset($result[0][$indexedBy]))
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
	 * Excecute a query.
	 * @return \OpenFlame\Dbal\Query - Provides a fluent interface.
	 */
	public function exec()
	{
		$this->query();

		return $this;
	}

	/**
	 * Get row count.
	 * @return int - Number of rows affected
	 */
	public function getRowCount()
	{
		return $this->statement->rowCount();
	}

	/*
	 * Get the last insert id.
	 * @return string - The last insert id.
	 */
	public function insertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * Excecute a query.
	 * 
	 * @throws \PDOException
	 */
	private function query()
	{
		if($this->limit > 0)
		{
			$this->sql .= "\nLIMIT {$this->limit}\nOFFSET {$this->offset}";
		}

		$this->statement = $this->pdo->prepare($this->sql);
		$this->statement->execute($this->params);
	}
}
