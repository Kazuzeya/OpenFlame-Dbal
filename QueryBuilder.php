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
 * OpenFlame Dbal - Query Builder
 * 	     Wraps around PDO to create an interface to query the database
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class QueryBuilder extends Query
{
	/*
	 * @var flag - Type of query (determined by the first clause)
	 */
	protected $type = -1;

	/*
	 * @var array - Fields to select
	 */
	protected $select = array();

	/*
	 * @var array - Tables that being affect in this query
	 */
	protected $tables = array();

	/*
	 * @var array - Sets
	 */
	protected $sets = array();

	/*
	 * @var array - Rows for insert
	 */
	protected $rows = array();

	/*
	 * @var array - Complex array for wheres
	 */
	protected $wheres = array();

	/*
	 * consts - Query types
	 */
	const TYPE_SELECT = 0;
	const TYPE_UPDATE = 1;
	const TYPE_INSERT = 2;
	const TYPE_MULTII = 3;
	const TYPE_DELETE = 4;
	const TYPE_UPSERT = 5;

	/*
	 * Normal constructor
	 * @param string $name - Connection name
	 */
	public function __construct($name = '')
	{
		parent::__construct($name);
	}

	/**
	 * Start a SELECT statement
	 * @param mixed - Fields to select
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function select($fields)
	{
		$this->select = array_merge($this->select, $this->normalizeArray($fields));
		$this->type = static::TYPE_SELECT;

		return $this;
	}

	/**
	 * Start an UPDATE statement
	 * @param mixed - tables
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function update($tables)
	{
		$this->tables = array_merge($this->tables, $this->normalizeArray($tables));
		$this->type = static::TYPE_UPDATE;

		return $this;
	}

	/**
	 * Start an INSERT statement
	 * @param mixed - tables
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function insert($table)
	{
		$this->tables = array_slice($this->normalizeArray($table), 0, 1);
		$this->type = static::TYPE_INSERT;

		return $this;
	}

	/**
	 * Start an INSERT statement
	 * @param mixed - tables
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function multiInsert($table)
	{
		$this->tables = array_slice($this->normalizeArray($table), 0, 1);
		$this->type = static::TYPE_MULTII;

		return $this;
	}

	/**
	 * Start an UPSERT statement
	 * @param mixed - tables
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function upsert($table)
	{
		$this->tables = array_slice($this->normalizeArray($table), 0, 1);
		$this->type = static::TYPE_UPSERT;

		return $this;
	}

	/**
	 * Start a DELETE statement
	 * @param mixed - tables
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function delete($tables)
	{
		$this->tables = $this->normalizeArray($tables);
		$this->type = static::TYPE_DELETE;

		return $this;
	}

	/**
	 * FROM clause
	 * @param mixed - tables
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function from($tables)
	{
		$this->tables = array_merge($this->tables, $this->normalizeArray($tables));

		return $this;
	}

	/**
	 * SET clause
	 * @param mixed - key/vals
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function set()
	{
		$args = func_get_args();

		switch($this->type)
		{
			case static::TYPE_MULTII:
				if(!is_array($args[0]))
				{
					throw new LogicException("Argument to QueryBuilder::set() must be an array when Multi-INSERTing.");
				}

				$this->rows[] = $args[0];
			break;
	
			case static::TYPE_INSERT:
				if(!is_array($args[0]))
				{
					throw new LogicException("Argument to QueryBuilder::set() must be an array when INSERTing.");
				}

				$this->rows[0] = $args[0];
			break;

			default:
				$this->sets = array_merge($this->sets, $this->inputKeyVals($args));
			break;
		}

		return $this;
	}

	/*
	 * WHERE clause
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides an fluent interface
	 */
	public function where($statement, $val = null)
	{
		$this->wheres[] = array('WHERE', $statement, $val);

		return $this;
	}

	/*
	 * WHERE clause
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides an fluent interface
	 */
	public function andWhere($statement, $val = null)
	{
		$this->wheres[] = array('AND', $statement, $val);

		return $this;
	}

	/*
	 * WHERE clause
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides an fluent interface
	 */
	public function orWhere($statement, $val)
	{
		$this->wheres[] = array('OR', $statement, $val = null);

		return $this;
	}

	/*
	 * Build the query 
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides an fluent interface
	 */
	public function build()
	{
		// Accumulators
		$sql = '';
		$params = array();

		$sets = $insert = $where = false;

		switch($this->type)
		{
			case static::TYPE_SELECT:
				$sql .= 'SELECT ' . implode(',', $this->select) . "\nFROM ";
				$where = true;
			break;

			case static::TYPE_UPSERT:	
			case static::TYPE_UPDATE:
				$sql .= 'UPDATE ';
				$sets = true;
				$where = true;
			break;

			case static::TYPE_MULTII:
			case static::TYPE_INSERT:
				$sql .= 'INSERT INTO ';
				$insert = true;
			break;

			case static::TYPE_DELETE:
				$sql .= 'DELETE FROM ';
				$where = true;
			break;;
		}

		// Tabletime
		$sql .= implode(', ', $this->tables) . "\n";

		// For inserts
		if ($insert && sizeof($this->rows[0]))
		{
			$_rows = array();
			$sql .= '(' . implode(',', array_keys($this->rows[0])) . ")\n";
			$_row = implode(',', array_fill(0,sizeof($this->rows[0]),'?'));

			foreach($this->rows as $i => $row)
			{
				foreach($row as $val)
				{
					$params[] = $val;
				}
				
				$_rows[$i] = $_row;
			}

			$sql .= 'VALUES (' . implode("),\n(", $_rows) . ')';
		}

		// Sets
		if ($sets && sizeof($this->sets))
		{
			$temp = array();

			foreach($this->sets as $col => $val)
			{
				if (is_null($val) || !strlen($col))
				{
					continue;
				}

				$temp[] = $col . ' = ?';
				$params[] = $val;
			}

			$sql .= 'SET ' . implode(',', $temp) . "\n";
			unset($temp);
		}

		// Where
		if ($where && sizeof($this->wheres))
		{
			foreach($this->wheres as $key => $val)
			{
				$sql .= $val[0] . ' ' . $val[1] . "\n";

				if(isset($val[2]) && !is_null($val[2]) && strpos($val[1], '?'))
				{
					$params[] = $val[2];
				}
			}
		}

		$this->sql($sql);
		$this->setParams($params);

		return $this;
	}

	/**
	 * Excecute a query, override of Query::exec()
	 * @return int - Number of rows affected
	 */
	public function exec()
	{
		$this->_query();

		$count = $this->stmt->rowCount();

		if(!$count && $this->type == static::TYPE_UPSERT)
		{
			// build insert from update
			$sql = 'INSERT INTO ' . (string) $this->tables . "\n";
			$sql .= '(' . implode(',', array_keys($this->sets)) . ")\n";

			$qs = array_fill(0,sizeof($this->sets),'?');
			$sql .= 'VALUES (' . implode(',', $qs) . ')';
		}

		return $count;
	}

	/**
	 * Used to (internally) normalize statements to an array
	 * @todo change foreach() to an array_map() implementation
	 * @param mixed - array or commma separated data
	 * @return array - Normalized data
	 */
	protected function normalizeArray($items)
	{
		if (!is_array($items))
		{
			$items = explode(',', $items);
		}

		$items = array_map('trim', $items);

		return $items;
	}

	/**
	 * Used to (internally) input data with a key/value relationship 
	 * @param mixed
	 * @return array - organized key/val
	 */
	protected function inputKeyVals($args)
	{
		$buffer = array();

		// We are an array('field'=>'value', ...)
		if (is_array($args[0]))
		{
			$buffer = $args[0];
		}
		// PDO prepared query style
		else if (strrchr($args[0], '?') && sizeof($args) > 1)
		{
			$sets = array_map('trim', explode(',', $args[0]));

			$matches = array();
			$i = 1;
			foreach($sets as $item)
			{
				if (!isset($args[$i]))
				{
					break;
				}

				preg_match("#^([a-z]+)[\s]*\=[\s]*\?$#i", $item, $matches);

				$buffer[$matches[1]] = $args[$i];
				$i++;
			}
		}
		// Single Key/value set
		else if (isset($args[0]) && isset($args[1]) && !isset($args[2]) && is_string($args[0]))
		{
			$buffer[$args[0]] = $args[1];
		}

		return $buffer;
	}
}
