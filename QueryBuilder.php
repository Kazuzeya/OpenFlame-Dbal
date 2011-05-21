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
	 * consts - Query types
	 */
	const TYPE_SELECT = 0;
	const TYPE_UPDATE = 1;
	const TYPE_INSERT = 2;
	const TYPE_DELETE = 3;
	const TYPE_UPSERT = 4;

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
}
