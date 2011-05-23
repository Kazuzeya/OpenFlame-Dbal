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
 * OpenFlame Dbal - Format CVS
 * 	     Encodes/Decodes the array into and out of an XML format
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Xml implements FormatInterface
{
	/*
	 */
	const EXT = 'xml';

	/*
	 * Encode to a format
	 * @param array $data - 2D array to be encoded
	 * @return string - Formated string for file output
	 * @throws /LogicException - (Potentially)
	 */
	public function encode($data)
	{
		$xml = new SimpleXMLElement("<data></data>");

		foreach($data as $row)
		{
			$xmlRow = $xml->addChild('row');

			foreach($row as $col => $val)
			{
				$xmlRow->addChild($col, $val);
			}
		}

		return $xml->asXML();
	}

	/*
	 * Decode to an array
	 * @param string $data - Formated string from file
	 * @return array - 2D array
	 * @throws /LogicException - (Potentially)
	 */
	public function decode($data)
	{
		$xml = new SimpleXMLElement($data);

		$result = array();
		foreach($xml as $row)
		{
			$temp = array();

			foreach($row as $col => $val)
			{
				$temp[$col] = (string) $val;
			}

			$result[] = $temp;
		}

		return $result;
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
