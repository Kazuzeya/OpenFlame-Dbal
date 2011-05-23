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
class Php implements FormatInterface
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
	}

	/*
	 * Decode to an array
	 * @param string $data - Formated string from file
	 * @return array - 2D array
	 * @throws /LogicException - (Potentially)
	 */
	public function decode($data)
	{
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
