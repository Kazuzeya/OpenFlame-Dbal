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
 * OpenFlame Dbal - Query
 * 	     Wraps around PDO to create an interface to query the database
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Query
{
	private $pdo; 

	public static function getInstance($id = 0)
	{
		return new static($id);
	}

	public function __construct($id = 0)
	{
		$this->pdo = \OpenFlame\Dbal\Connection::get($id);
	}

	public function sql($sql)
	{
		return $this;
	}

	public function exec()
	{
		return $this;
	}
}
