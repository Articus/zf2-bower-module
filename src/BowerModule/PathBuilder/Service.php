<?php

namespace BowerModule\PathBuilder;
use BowerModule\Config;
use BowerModule\ServiceRetriever;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Helper service for building paths to files
 */
class Service implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	use ServiceRetriever\ServiceLocatorTrait;
	use Config\ServiceAwareTrait;

	const WEB_PATH_SEPARATOR = '/';
	const OS_PATH_SEPARATOR = DIRECTORY_SEPARATOR;

	/**
	 * Return OS filesystem path built from specified components
	 * @param string $folderOrFile,... - path components
	 * @return string
	 */
	public function getOsPath($folderOrFile)
	{
		return $this->getPath(self::OS_PATH_SEPARATOR, func_get_args());
	}

	/**
	 * Returns web path built from specified components
	 * @param string $folderOrFile,... - path components
	 * @return string
	 */
	public function getWebPath($folderOrFile)
	{
		return $this->getPath(self::WEB_PATH_SEPARATOR, func_get_args());
	}

	/**
	 * Returns path from specified components
	 * @param string $separator - string to separate path components
	 * @param string[] $components - path components
	 * @return string
	 */
	protected function getPath($separator, array $components)
	{
		$lastIndex = count($components) - 1;
		$trimAndCheck = function($component, $index) use ($separator, $lastIndex)
		{
			$result = '';
			switch ($index)
			{
				case  0:
					$result = rtrim($component, $separator);
					break;
				case $lastIndex:
					$result = ltrim($component, $separator);
					break;
				default:
					$result = trim($component, $separator);
					break;
			}
			if (empty($result))
			{
				throw new \RuntimeException('Empty path component');
			}
			return $result;
		};
		if (!array_walk($components, $trimAndCheck))
		{
			throw new \RuntimeException('Failed to process path components');
		}
		return implode($separator, $components);
	}

	/**
	 * Returns name of the file where bower module source code is stored
	 * @param string $moduleName
	 * @return string
	 */
	public function getModuleFileName($moduleName)
	{
		return $moduleName.'.js';
	}

	/**
	 * Returns name of the file where pack combined minified code is stored
	 * @param string $packName
	 * @return string
	 */
	public function getPackFileName($packName)
	{
		return $packName.'.min.js';
	}

	/**
	 * Returns OS filesystem path to debug version of bower module JS file
	 * @param string $moduleName
	 * @return string
	 */
	public function getDebugOsPath($moduleName)
	{
		$folder = $this->getConfig()->getDebugFolder();
		if ((!($folder instanceof Config\Folder)) || empty($folder->getOs()))
		{
			throw new \RuntimeException('Invalid debug OS folder configuration.');
		}
		return $this->getOsPath($folder->getOs(), $this->getModuleFileName($moduleName));
	}

	/**
	 * Returns web path to debug version of bower module JS file
	 * @param string $moduleName
	 * @return string
	 */
	public function getDebugWebPath($moduleName)
	{
		$folder = $this->getConfig()->getDebugFolder();
		if ((!($folder instanceof Config\Folder)) || empty($folder->getWeb()))
		{
			throw new \RuntimeException('Invalid debug web folder configuration.');
		}
		return $this->getWebPath($folder->getWeb(), $this->getModuleFileName($moduleName));
	}

	/**
	 * Returns web path to pack JS file
	 * @param string $packName
	 * @return string
	 */
	public function getPackOsPath($packName)
	{
		$folder = $this->getConfig()->getPackFolder();
		if ((!($folder instanceof Config\Folder)) || empty($folder->getOs()))
		{
			throw new \RuntimeException('Invalid pack os folder configuration.');
		}
		return $this->getOsPath($folder->getOs(), $this->getPackFileName($packName));
	}

	/**
	 * Returns OS file system path to pack JS file
	 * @param string $packName
	 * @return string
	 */
	public function getPackWebPath($packName)
	{
		$folder = $this->getConfig()->getPackFolder();
		if ((!($folder instanceof Config\Folder)) || empty($folder->getWeb()))
		{
			throw new \RuntimeException('Invalid pack web folder configuration.');
		}
		return $this->getWebPath($folder->getWeb(), $this->getPackFileName($packName));
	}
}