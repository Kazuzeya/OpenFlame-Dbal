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
use \OpenFlame\Dbal\Query;

/**
 * OpenFlame Dbal - sqlsrv Query,
 * 	     DBMS specific extension of \OpenFlame\Dbal\Query.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */

class sqlsrv extends Query
{
	/**
	 * Excecute a query.
	 */
	private function query()
	{
		// Handle limits/offsets.
		if($this->limit > 0)
		{
			preg_match("#^(SELECT(\s+DISTINCT)?)#i", $this->sql, $type);
			preg_match("#ORDER\s+BY\s+([a-z0-9]+(,\s*[a-z0-9]+)*)#i", $this->sql, $orderBys);

			$sql =  $type[1] . ' TOP ' . ($this->limit + $this->offset);
			$sql .= " ROW_NUMBER() OVER (ORDER BY {$orderBys[1]}) AS _row_num, ";
			$sql .= substr($this->sql, strlen($type[1]));
			$sql .= (strpos($sql, 'WHERE') !== false) ? ' WHERE _row_num >= ' . $offset : ' AND _row_num >= ' . $offset;

			$this->sql = $sql;
		}

		$this->statement = $this->pdo->prepare($this->sql);
		$this->statement->execute($this->params);
	}
}
