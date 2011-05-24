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

namespace OpenFlame\Dbal\Format\Types;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Dbal - Format CSV
 * 	     Encodes/Decodes the array into and out of a CSV format
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Csv implements FormatInterface
{
	/*
	 */
	const EXT = 'csv';

	/*
	 * Encode to a format
	 * @param array $data - 2D array to be encoded
	 * @return string - Formated string for file output
	 * @throws /LogicException - (Potentially)
	 */
	public function encode($data)
	{
		$buffer = '';

		foreach(array_keys($data[0]) as $col)
		{
			$buffer .= '"' . $col . '",';
		}

		$buffer = substr($buffer, 0, strlen($buffer) - 1);
		$buffer .= "\n";
	
		foreach($data as $row)
		{
			foreach($row as $item)
			{
				$buffer .= '"' . addslashes($item) . '",';
			}

			$buffer = substr($buffer, 0, strlen($buffer) - 1);
			$buffer .= "\n";
		}

		return $buffer;
	}

	/*
	 * Decode to an array
	 * @param string $data - Formated string from file
	 * @return array - 2D array
	 * @throws /LogicException - (Potentially)
	 */
	public function decode($data)
	{
		$pieces = explode("\n", $data);
		$cols = explode('","', array_shift($pieces));
		$itemcount = sizeof($cols);
		$cols[0] = substr($cols[0], 1, strlen($cols[0]));
		$cols[$itemcount-1] = substr($cols[$itemcount-1], 0, strlen($cols[$itemcount-1]) - 1);

		$buffer = array();

		foreach($pieces as $index => $row)
		{
			if(!strlen($row))
			{
				break;
			}
	
			$rowAry = explode('","', $row);
			$rowAry[0] = substr($rowAry[0], 1, strlen($rowAry[0]));
			$rowAry[$itemcount-1] = substr($rowAry[$itemcount-1], 0, strlen($rowAry[$itemcount-1]) - 1);

			for($i = 0; $i < $itemcount; $i++)
			{
				$buffer[$index][$cols[$i]] = stripslashes($rowAry[$i]);
			}
		}

		return $buffer;
	}

	/*
	 * Get the file extention
	 * @return string - File extention (without the dot)
	 */
	public function getExt()
	{
		return self::EXT;
	}
}
