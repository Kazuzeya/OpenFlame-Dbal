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
 * OpenFlame Dbal - Query
 * 	     Wraps around PDO to create an interface to query the database
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
	private $pdo = NULL;

	/**
	 * Statement for our query 
	 * @var PDOStatement 
	 */
	private $smt = NULL;

	/**
	 * Driver for the connection
	 * @var string 
	 */
	protected $driver = '';

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
		return new static($name);
	}

	/**
	 * Normal constructor
	 * @param string $name - Connection name
	 */
	public function __construct($name = '')
	{
		$conn = Connection::getInstance($name);

		$this->pdo = $conn->get();
		$this->driver = $conn->getDriver();
	}

	/**
	 * Set the SQL to be ran
	 * @param string $sql 
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
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
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
	 */
	public function limit($limit)
	{
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Offset the result set
	 * @param int offset 
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
	 */
	public function offset($offset)
	{
		$this->offset = (int) $offset;

		return $this;
	}

	/**
	 * Set params
	 * @param array parameters
	 * @return \OpenFlame\Dbal\Query - provides fluent interface 
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
		if ($this->smt == NULL)
		{
			$this->query();
		}

		return $this->smt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Query and fetch the rowset
	 * @return array - Multi-dimensional associative array of the rowset being fetched
	 */
	public function fetchRowset()
	{
		$this->query();

		return $this->smt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Excecute a query
	 * @return int - Number of rows affected
	 */
	public function exec()
	{
		$this->query();

		return $this->smt->rowCount();
	}

	/*
	 * Get the last insert id
	 * @return string - Insert ID
	 */
	public function insertId()
	{
		if ($this->driver == 'pgsql' && preg_match("#^INSERT\s+INTO\s+([a-z0-9\_\-]+)\s+#i", $this->sql, $table))
		{
			// We're using currval() here to grab that ID.
			// http://www.postgresql.org/docs/8.2/interactive/functions-sequence.html
			$result = $this->pdo->query("SELECT currval('{$table[1]}_seq') AS _last_insert_id");
			return (strlen($result->fetchColumn()) < 1) ? (int) $result['_last_insert_id'] : false;
		}

		return $this->pdo->lastInsertId();
	}

	/**
	 * Excecute a query (internally)
	 * @throws PDOException
	 */
	private function query()
	{
		// Handle limits/offsets
		if ($this->limit > 0)
		{
			switch ($this->driver)
			{
				case 'mssql':
					preg_match("#^(SELECT(\s+DISTINCT)?)#i", $this->sql, $type);
					preg_match("#ORDER\s+BY\s+([a-z0-9]+(,\s*[a-z0-9]+)*)#i", $this->sql, $orderBys);
					
					$sql =  $type[1] . ' TOP ' . ($this->limit + $this->offset);
					$sql .= " ROW_NUMBER() OVER (ORDER BY {$orderBys[1]}) AS _row_num, ";
					$sql .= substr($this->sql, strlen($type[1]));

					$this->sql = $sql;
				break;
				
				default:
					$this->sql .= "\nLIMIT {$this->limit}\nOFFSET {$this->offset}";
				break;
			}
		}

		$this->smt = $this->pdo->prepare($this->sql);
		$this->smt->execute($this->params);
	}
}
