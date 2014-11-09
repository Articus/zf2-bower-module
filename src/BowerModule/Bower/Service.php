<?php

namespace BowerModule\Bower;

use BowerModule\Config;
use BowerModule\PathBuilder;
use BowerModule\ServiceRetriever;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Helper service for dealing with bower files
 */
class Service implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	use ServiceRetriever\ServiceLocatorTrait;
	use Config\ServiceAwareTrait;
	use PathBuilder\ServiceAwareTrait;

	/**
	 * @var string
	 */
	protected $bowerFolder;

	/**
	 * Returns path to bower folder
	 * @return string
	 */
	public function getBowerFolder()
	{
		if (empty($this->bowerFolder))
		{
			$folder = $this->getConfig()->getBowerFolder();
			if ((!($folder instanceof Config\Folder)) || empty($folder->getOs()))
			{
				throw new \RuntimeException('Invalid bower OS folder configuration.');
			}
			if (!is_readable($folder->getOs()))
			{
				throw new \RuntimeException('Can not access bower folder: "'.$folder->getOs().'".');
			}
			$this->bowerFolder = $folder->getOs();
		}
		return $this->bowerFolder;
	}

	/**
	 * Returns list of all bower modules that are required to use the target list of modules
	 * @param string[] $modules - list of bower modules' names
	 * @return string[]
	 */
	public function findDependencies(array $modules)
	{
		$result = [];
		$head = new Node('', 0);
		$tail = $head;
		/** @var Node[] $names */
		$names = [];
		foreach ($modules as $moduleName)
		{
			$node = new Node($moduleName, 1);
			$tail->next = $node;
			$node->prev = $tail;
			$tail = $node;
			$names[$moduleName] = $node;
		}
		/** @var Node[] $bookmarks */
		$bookmarks = [$head, $head->next];
		$cursor = $head->next;
		while ($cursor)
		{
			foreach ($this->findDirectDependencies($cursor->name) as $moduleName)
			{
				$dependency = null;
				if (isset($names[$moduleName]))
				{
					$dependency = $names[$moduleName];
				}
				else
				{
					$dependency = new Node($moduleName, 0);
					$names[$dependency->name] = $dependency;
				}
				if ($dependency->level < $cursor->level + 1)
				{
					if ($dependency->next instanceof Node)
					{
						$dependency->next->prev = $dependency->prev;
					}
					if ($dependency->prev instanceof Node)
					{
						$dependency->prev->next = $dependency->next;
						if (is_null($dependency->next))
						{
							$tail = $dependency->prev;
						}
					}
					if (($bookmarks[$dependency->level] instanceof Node)
						&& ($bookmarks[$dependency->level]->name == $dependency->name)
					)
					{
						$bookmarks[$dependency->level] = $dependency->next;
					}
					$dependency->level = $cursor->level + 1;
					if (isset($bookmarks[$dependency->level])
						&& ($bookmarks[$dependency->level] instanceof Node)
					)
					{
						$anchor = $bookmarks[$dependency->level];
						$dependency->prev = $anchor->prev;
						$dependency->next = $anchor;
					}
					else
					{
						$dependency->prev = $tail;
						$dependency->next = null;
						$tail = $dependency;
					}
					$bookmarks[$dependency->level] = $dependency;
					if ($dependency->next instanceof Node)
					{
						$dependency->next->prev = $dependency;
					}
					if ($dependency->prev instanceof Node)
					{
						$dependency->prev->next = $dependency;
					}
				}
			}
			$cursor = $cursor->next;
		}

		$cursor = $tail;
		while ($cursor && $cursor->name)
		{
			$result[] = $cursor->name;
			$cursor = $cursor->prev;
		}

		return $result;
	}

	/**
	 * Returns list of direct module dependencies
	 * @param string $module
	 * @return string[]
	 */
	public function findDirectDependencies($module)
	{
		$result = [];
		$config = $this->getModuleBowerConfig($module);
		if (isset($config['dependencies']) && is_array($config['dependencies']))
		{
			$result = array_keys($config['dependencies']);
		}
		return $result;
	}

	/**
	 * Returns file paths to bower module JS files
	 * @param string $module - bower module name
	 * @param bool $minified - flag if minified version of files is needed
	 * @return string[]
	 */
	public function getModulePaths($module, $minified = false)
	{
		$result = [];
		$config = $this->getModuleBowerConfig($module);

		$paths = $config['main'];
		if (!is_array($paths))
		{
			$paths = [$paths];
		}
		foreach ($paths as $path)
		{
			if (is_string($path) && fnmatch('*.js', $path, FNM_CASEFOLD))
			{
				$fullPath = $this->getPathBuilder()->getOsPath($this->getBowerFolder(), $module, $path);
				if ($minified)
				{
					//TODO make more advanced detection or minify source with Closure Compiler Service
					//This is only an assumption based on the most common scenario
					$fullPath = mb_substr($fullPath, 0, mb_strlen($fullPath) - 3).'.min'.mb_substr($fullPath, -3);
				}

				if (!is_readable($fullPath))
				{
					throw new \RuntimeException('Failed to access file "'.$fullPath.'".');
				}
				$result[] = $fullPath;
			}
		}
		return $result;
	}

	/**
	 * Returns path to bower.json for specified module
	 * @param string $module
	 * @return string
	 */
	public function getModuleBowerConfigFilePath($module)
	{
		$path = $this->getPathBuilder()->getOsPath($this->getBowerFolder(), $module, 'bower.json');
		if(!file_exists($path))
			$path = $this->getPathBuilder()->getOsPath($this->getBowerFolder(), $module, 'package.json');
		return $path;
	}

	/**
	 * Returns content of bower.json for specified module as assoc array
	 * @param string $module
	 * @return array
	 */
	public function getModuleBowerConfig($module)
	{
		//TODO add caching
		$filePath = $this->getModuleBowerConfigFilePath($module);
		if (!is_readable($filePath))
		{
			throw new \RuntimeException('Failed to access configuration for "'.$module.'".');
		}
		$config = file_get_contents($filePath);
		if ($config === false)
		{
			throw new \RuntimeException('Failed to read configuration for "'.$module.'".');
		}
		$config = json_decode($config, true);
		if ((!is_array($config)) || empty($config['main']))
		{
			throw new \RuntimeException('Invalid configuration for "'.$module.'".');
		}
		return $config;
	}
}