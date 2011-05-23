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

namespace OpenFlame\Dbal\Format;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Dbal - Formats
 * 	     Provides infrastructure for data to be read and written to different formats
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Dbal
 */
class Driver
{
	/*
	 * @var static singleton instance
	 */
	private static $instance = null;

	/*
	 * @var format instance 
	 */
	private $format;

	/*
	 */
	const HEADER	= 'OpenFlame Database Abstration %s Export';
	const COPYRIGHT	= '(c) 2011 http://openflame-project.org/';
	const LICENSE	= 'http://opensource.org/licenses/mit-license.php The MIT License';

	/*
	 * Static constructor
	 * @param string $name - Name of the connection, or empty if using the default 
	 * @return \OpenFlame\Dbal\Connection - Specific instance of this class specified by the $name param  
	 */
	public static function getInstance()
	{
		if (static::$instance == null)
		{
			static::$instance = new static();
		}

		return $instance;
	}

	/*
	 * Set the format
	 * @param \OpenFlame\Dbal\Format\Types\FormatInterface object - Instance of the format to set
	 * @return \OpenFlame\Dbal\Format\Driver - Provides a fluent interface
	 */
	public function setFormat(\OpenFlame\Dbal\Format\Types\FormatInterface $obj)
	{
		$this->format = $obj;

		return $this;
	}

	/*
	 * To array
	 * @param string $data - data to be encoded from external format
	 * @return array - data encoded to a PHP array
	 */
	public function toArray($data)
	{
		return $this->format->decode($data);
	}

	/*
	 * To a format
	 * @param string $data - data to be encoded to an external format
	 * @return array - data encoded in the format specified
	 */
	public function toFormat($data)
	{
		return $this->format->encode($data);
	}
}
