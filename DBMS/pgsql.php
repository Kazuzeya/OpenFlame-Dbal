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

namespace OpenFlame\Dbal\DBMS;

use \PDO;
use \LogicException;
use \RuntimeException;
use \OpenFlame\Dbal\Query;

/**
 * OpenFlame Dbal - PgSQL Query,
 * 	     DBMS specific extension of \OpenFlame\Dbal\Query.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */

class pgsql extends Query
{
	/*
	 * Get the last insert id for PgSQL
	 * @return string - Insert ID
	 */
	public function insertId()
	{
		if (preg_match("#^INSERT\s+INTO\s+([a-z0-9\_\-]+)\s+#i", $this->sql, $table))
		{
			// We're using currval() here to grab that ID.
			// The only requirement is that the sequencer MUST be {tablename}_seq
			// http://www.postgresql.org/docs/8.2/interactive/functions-sequence.html
			$result = $this->pdo->query("SELECT currval('{$table[1]}_seq') AS _last_insert_id");
			return ($result !== false) ? (int) $result->fetchColumn(0) : false;
		}

		return false;
	}
}
