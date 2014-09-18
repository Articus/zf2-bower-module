<?php

namespace BowerModule\View\Helper;

use BowerModule\Bower as BowerService;
use BowerModule\Config;
use BowerModule\PathBuilder;
use BowerModule\ServiceRetriever;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\HeadScript as ScriptContainer;

class Bower extends AbstractHelper implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	use ServiceRetriever\PluginManagerTrait;
	use BowerService\ServiceAwareTrait;
	use Config\ServiceAwareTrait;
	use PathBuilder\ServiceAwareTrait;

	const CONTAINER_HEAD = 'head';
	const CONTAINER_INLINE = 'inline';

	const PLACE_APPEND = 'append';
	const PLACE_PREPEND = 'prepend';

	/**
	 * Inserts all JS-files required to use pack sorted in the order they should be loaded.
	 * @param string $packName - pack defined in configuration
	 * @param string $containerName - script view helper that should be used to insert files
	 * @param string|number $place - placement for files in view helper either PLACE_APPEND or PLACE_PREPEND or offset index
	 */
	public function __invoke($packName, $containerName = self::CONTAINER_INLINE, $place = self::PLACE_APPEND)
	{
		$container = $this->getScriptContainer($containerName);
		$jsFiles = $this->getJSFiles($packName);

		$insert = null;
		switch ($place)
		{
			case self::PLACE_APPEND:
				$insert = function($jsFile) use ($container)
				{
					$container->appendFile($jsFile);
				};
				break;
			case self::PLACE_PREPEND:
				$insert = function($jsFile) use ($container)
				{
					$container->prependFile($jsFile);
				};
				break;
			default:
				if (is_numeric($place))
				{
					$insert = function($jsFile) use ($container, &$place)
					{
						$container->offsetSetFile($place, $jsFile);
						$place++;
					};
				}
				break;
		}
		if (is_callable($insert))
		{
			foreach ($jsFiles as $jsFile)
			{
				$insert($jsFile);
			}
		}
		else
		{
			throw new \RuntimeException('Failed to insert JS files.');
		}
		return $this;
	}

	/**
	 * Return list of paths to all JS-files required to use specified pack sorted in the order they should be loaded.
	 * @param string $packName
	 * @return string[]
	 */
	protected function getJSFiles($packName)
	{
		$result = [];

		$config = $this->getConfig();
		$bower = $this->getBower();
		$pathBuilder = $this->getPathBuilder();

		$packs = $config->getPacks();
		if (isset($packs[$packName]))
		{
			$pack = $packs[$packName];
			$pathModifier = function($path) use ($pack)
			{
				$token = $pack->getToken();
				if (!empty($token))
				{
					$path .= '?t='.$token;
				}
				return $path;
			};
			if ($config->getDebugMode())
			{
				$modules = $bower->findDependencies($pack->getModules());
				foreach ($modules as $moduleName)
				{
					$result[] = $pathModifier($pathBuilder->getDebugWebPath($moduleName));
				}
			}
			else
			{
				$result[] = $pathModifier($pathBuilder->getPackWebPath($packName));
			}
		}
		return $result;
	}

	/**
	 * Returns instance of specified script view helper
	 * @param string $containerName
	 * @return ScriptContainer
	 */
	protected function getScriptContainer($containerName)
	{
		$containerPluginName = '';
		switch ($containerName)
		{
			case self::CONTAINER_HEAD:
				$containerPluginName = 'headScript';
				break;
			case self::CONTAINER_INLINE:
				$containerPluginName = 'inlineScript';
				break;
			default:
				throw new \RuntimeException('Invalid script container: "'.$containerName.'".');
		}

		$result = $this->getPluginManager()->get($containerPluginName);
		if (!($result instanceof ScriptContainer))
		{
			throw new \RuntimeException('Failed to retrieve script container "'.$containerPluginName.'".');
		}
		return $result;
	}
}