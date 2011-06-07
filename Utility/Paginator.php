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

namespace OpenFlame\Dbal\Utility;

if (!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Dbal - Paginator
 * 	     Provides a nice interface for paginating queries
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Paginator
{
	/*
	 * @var per page limit
	 */
	protected $limit = 50;

	/*
	 * @var selection page number
	 */
	protected $pageNumber = 0;

	/*
	 * @var total pages
	 */
	protected $totalPages = 0;

	/*
	 * @var total records
	 */
	protected $totalRecords = 0;

	/*
	 * Get instance
	 */
	public static function getInstance()
	{
		return new static();
	}

	/*
	 * Set the base query
	 * @param mixed $query - Instance of Query, QueryBuilder, or a string
	 * @return \OpenFlame\Dbal\Utility\Paginator - provides fluent interface 
	 */
	public function setQuery($query)
	{
	}

	/*
	 * Set the page limit
	 * @param int $limit - Limit for selection (number of items/page)
	 * @return \OpenFlame\Dbal\Utility\Paginator - provides fluent interface 
	 */
	public function setPageLimit($limit)
	{
		$this->limit = (int) $limit;

		return $this;
	}

	/*
	 * Set the current page number
	 * @param int $limit - Limit for selection (number of items/page)
	 * @return \OpenFlame\Dbal\Utility\Paginator - provides fluent interface 
	 */
	public function setPageNumber($page)
	{
		$this->pageNumber = (int) $page;

		return $this;
	}

	/*
	 * Fetch the row (Itteration safe)
	 * @return array - Associative array representing the current row
	 */
	public function fetchRow()
	{
	}

	/*
	 * Fetch the rowset
	 * @return array - Multi-dimensional array representing the row set returned
	 */
	public function fetchRowset()
	{
	}

	/*
	 * Get the current page number
	 * @return int - page number
	 */
	public function getCurrentPage()
	{
		return $this->pageNumber;
	}

	/*
	 * Get the total page count
	 * @return int - page count
	 */
	public function getTotalPages()
	{
	}

	/*
	 * Get the total record count
	 * @return int - record count
	 */
	public function getTotalRecords()
	{
	}

	/*
	 * Do the actuall query
	 */
	private function _query()
	{
	}
}
