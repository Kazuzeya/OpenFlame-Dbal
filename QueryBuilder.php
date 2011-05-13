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
		$this->select = $this->normalizeArray($fields);

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
		$this->tables = $this->normalizeArray($tables);
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
		$this->tables = reset($this->normalizeArray($table));
		$this->type = static::TYPE_INSERT;

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

		return $this;
	}

	/**
	 * FROM clause
	 * @param mixed - Single table
	 * @return \OpenFlame\Dbal\QueryBuilder - Provides a fluent interface.
	 */
	public function from($tables)
	{
		$this->tables = $this->normalizeArray($tables);

		return $this;
	}

	/**
	 * Used to (internally) normalize statements to an array
	 * @param mixed - array or commma separated table(s)
	 * @return array - Normalized data
	 */
	protected function normalizeArray($items)
	{
		if(!is_array($items))
		{
			$items = explode(',', $items);
		}

		foreach($items as $k => $item)
		{
			$items[$k] = trim($item);
		}

		return $items;
	}
}
