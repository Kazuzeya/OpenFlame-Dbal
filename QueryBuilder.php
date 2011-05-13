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
	 * @var array - Values to be set (or inserted)
	 */
	protected $sets = array();

	/*
	 * @var array - Where conditions
	 */
	protected $where = array();

	/*
	 * @var array - Where conditions
	 */
	protected $whereIns = array();

	/*
	 * @var array - start offset
	 */
	protected $starts = array();

	/*
	 * @var array - limits
	 */
	protected $limits = array();

	/*
	 * @var array - Order by fields 
	 */
	protected $orderBy = array();

	/*
	 * @var string - Order by direction
	 */
	protected $orderDir = 'ASC';

	/*
	 * consts - Query types
	 */
	const TYPE_SELECT = 0;
	const TYPE_UPDATE = 1;
	const TYPE_INSERT = 2;
	const TYPE_DELETE = 3;
	const TYPE_UPSERT = 4;

	/**
	 * Statically get an instance of this object
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public static function getInstance()
	{
		return new static();
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
	 * @param mixed - Single table
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function from($tables)
	{
		$this->tables = array_merge($this->tables, $this->normalizeArray($tables));

		return $this;
	}

	/**
	 * SET clause
	 * @param mixed
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function set()
	{
		$this->sets = array_merge($this->sets, $this->inputKeyVals(func_get_args()));

		return $this;
	}

	/**
	 * WHERE clause
	 * @param mixed
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function where()
	{
		$this->where = array_merge($this->where, $this->inputKeyVals(func_get_args()));

		return $this;
	}

	/**
	 * WHEREIN clause
	 * @param mixed
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function whereIn($feild, $vals)
	{
		$this->whereIns[] = array($feild, $vals);
	}

	/**
	 * OR clause
	 * @param mixed
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function orWhere()
	{
	}

	/**
	 * ORDER BY clause
	 * @param mixed
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function orderBy()
	{
	}

	/**
	 * ORDER BY clause
	 * @param mixed
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function orderDirection($dir)
	{
		$orderDir = ($dir == 'ASC') ? 'ASC' : 'DESC';

		return $this;
	}

	public function d() {var_dump($this->sets);}

	/**
	 * Query and fetch a row 
	 * @note - Safe for itteration
	 * @return array - Associative array of the row being fetched
	 */
	public function fetchRow()
	{
	}

	/**
	 * Query and fetch a row
	 * @return array - Multi-dimensional associative array of the rowset being fetched
	 */
	public function fetchRowset()
	{
	}

	/**
	 * Excecute a query
	 * @return int - Number of rows affected
	 */
	public function exec()
	{
	}

	/**
	 * Used to (internally) normalize statements to an array
	 * @todo change foreach() to an array_map() implementation
	 * @param mixed - array or commma separated data
	 * @return array - Normalized data
	 */
	protected function normalizeArray($items)
	{
		if(!is_array($items))
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

		// We are array('field'=>'value', ...)
		if(is_array($args[0]))
		{
			$buffer = $args[0];
		}
		// Sprintf style
		else if(preg_match("#\%(s|d|i|f)#", $args[0]) > 0)
		{
			$sets = array_map('trim', explode(',', $args[0]));

			$matches = array();
			$i = 1;
			foreach($sets as $item)
			{
				if(!isset($args[$i]))
				{
					break;
				}

				preg_match("#^([a-z]+)[\s]*\=[\s]*\%(s|d|i|f)$#i", $item, $matches);

				$buffer[$matches[1]] = $args[$i];
				$i++;
			}
		}
		// Single Key/value set
		else if(isset($args[0]) && isset($args[1]) && is_string($args[0]))
		{
			$buffer[$args[0]] = $args[1];
		}

		return $buffer;
	}
}
