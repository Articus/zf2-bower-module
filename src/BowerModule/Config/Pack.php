<?php

namespace BowerModule\Config;

use Zend\Stdlib\AbstractOptions;

/**
 * Configuration options for packs of JS modules obtained via bower
 */
class Pack extends AbstractOptions
{
	/**
	 * Random token that should be appended to pack JS file URI.
	 * Pack hash shown by "bower prepare-packs" is a good choice for this field.
	 * @var string
	 */
	protected $token;
	/**
	 * List of bower module names that should be included in pack.
	 * @var string[]
	 */
	protected $modules;

	/**
	 * @param string $token
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param string[] $modules
	 */
	public function setModules($modules)
	{
		$this->modules = $modules;
	}

	/**
	 * @return string[]
	 */
	public function getModules()
	{
		return $this->modules;
	}

}