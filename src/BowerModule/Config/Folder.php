<?php

namespace BowerModule\Config;


use Zend\Stdlib\AbstractOptions;

/**
 * Configuration options for file folders
 */
class Folder extends AbstractOptions
{
	/**
	 * OS filesystem path to folder
	 * @var string
	 */
	protected $os;
	/**
	 * Web path to folder
	 * @var string
	 */
	protected $web;

	/**
	 * @param string $os
	 */
	public function setOs($os)
	{
		$this->os = $os;
	}

	/**
	 * @return string
	 */
	public function getOs()
	{
		return $this->os;
	}

	/**
	 * @param string $web
	 */
	public function setWeb($web)
	{
		$this->web = $web;
	}

	/**
	 * @return string
	 */
	public function getWeb()
	{
		return $this->web;
	}
}