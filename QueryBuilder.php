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
 * OpenFlame Dbal - Query Builder
 * 	     Wraps around PDO to create an interface to query the database
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class QueryBuilder
{
	/*
	 * Instance of \OpenFlame\Dbal\Query() (or an extending class)
	 * @var object \OpenFlame\Dbal\Query()
	 */
	private $query;

	/*
	 * Get a new instnace the fluent interface way
	 * @param object (optional) - Instance of a custom Driver inheriting \OpenFlame\Dbal\Query()
	 * -- OR --
	 * @param string (optional) - Connection name, defaults to default conneciton
	 *
	 * @return instance of QueryBuilder
	 */
	public static function newInstance($arg = null)
	{
		return new static($arg);
	}

	/*
	 * Constructor
	 * @param object (optional) - Instance of a custom Driver inheriting \OpenFlame\Dbal\Query()
	 * -- OR --
	 * @param string (optional) - Connection name, defaults to default conneciton
	 */
	public function __construct($arg = null)
	{
		// Check to see if it is an instance of Query (or a class extending that)
		if ($arg instanceof \OpenFlame\Dbal\Query)
		{
			$this->query = $arg;
		}
		// Next is default query handing with a non-default connection
		else
		{
			$name = (string) $arg;
			$class = '\\OpenFlame\\Dbal\\DBMS\\' . Connection::getInstance($name)->getDriver();
			$this->query = new $class($name);
		}
	}
}
