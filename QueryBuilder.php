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

/**
 * OpenFlame Dbal - Query Builder,
 * 	     Wraps around PDO to create an interface to query the database.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class QueryBuilder
{
	/*
	 * @var object \OpenFlame\Dbal\Query - Instance of \OpenFlame\Dbal\Query or an extending class.
	 */
	private $query;

	/*
	 * Get a new instance the fluent interface way.
	 * @param object (optional) - Instance of a custom driver inheriting \OpenFlame\Dbal\Query.
	 * -- OR --
	 * @param string (optional) - Connection name, defaults to default conneciton.
	 *
	 * @return \OpenFlame\Dbal\QueryBuilder
	 */
	public static function newInstance($arg = NULL)
	{
		return new static($arg);
	}

	/*
	 * Constructor.
	 * @param object (optional) - Instance of a custom driver inheriting \OpenFlame\Dbal\Query.
	 * -- OR --
	 * @param string (optional) - Connection name, defaults to default conneciton.
	 */
	public function __construct($arg = NULL)
	{
		// Check to see if it is an instance of \OpenFlame\Dbal\Query, or a class extending.
		if($arg instanceof \OpenFlame\Dbal\Query)
		{
			$this->query = $arg;
		}
		// Default query handling with a non-default connection.
		else
		{
			$name = (string) $arg;
			$class = '\\OpenFlame\\Dbal\\DBMS\\' . Connection::getInstance($name)->getDriver();
			$this->query = new $class($name);
		}
	}
}
