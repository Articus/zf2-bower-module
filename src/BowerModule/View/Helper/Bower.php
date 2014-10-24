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
	 * @param string $type - "$type" parameter for script view helper
	 * @param array $attrs - "$attrs" parameters for script view helper
	 * @return self
	 */
	public function __invoke(
		$packName,
		$containerName = self::CONTAINER_INLINE,
		$place = self::PLACE_APPEND,
		$type = null,
		$attrs = null
	)
	{
		$container = $this->getScriptContainer($containerName);
		$injector = $this->getInjector($container, $place);

		$config = $this->getConfig();
		$pathBuilder = $this->getPathBuilder();

		$packs = $config->getPacks();
		if (isset($packs[$packName]))
		{
			$pack = $packs[$packName];
			$insert = function($path) use ($injector, $pack, $type, $attrs)
			{
				//Add token to path
				$token = $pack->getToken();
				if (!empty($token))
				{
					$path .= '?t='.$token;
				}
				//Default value for type
				if (is_null($type))
				{
					$type = $pack->getType();
				}
				//Default value for attrs
				if (is_null($attrs))
				{
					$attrs = $pack->getAttributes();
				}
				$injector($path, $type, $attrs);
			};
			if ($config->getDebugMode())
			{
				$bower = $this->getBower();
				$modules = $bower->findDependencies($pack->getModules());
				foreach ($modules as $moduleName)
				{
					$insert($pathBuilder->getDebugWebPath($moduleName));
				}
			}
			else
			{
				$insert($pathBuilder->getPackWebPath($packName));
			}
		}

		return $this;
	}

	/**
	 * @param ScriptContainer $container
	 * @param string|number $place
	 * @return callable
	 */
	protected function getInjector($container, $place)
	{
		$insert = null;
		switch ($place)
		{
			case self::PLACE_APPEND:
				$insert = function($jsFile, $type, $attrs) use ($container)
				{
					$container->appendFile($jsFile, $type, $attrs);
				};
				break;
			case self::PLACE_PREPEND:
				$insert = function($jsFile, $type, $attrs) use ($container)
				{
					$container->prependFile($jsFile, $type, $attrs);
				};
				break;
			default:
				if (is_numeric($place))
				{
					$insert = function($jsFile, $type, $attrs) use ($container, &$place)
					{
						$container->offsetSetFile($place, $jsFile, $type, $attrs);
						$place++;
					};
				}
				break;
		}
		if (!is_callable($insert))
		{
			throw new \RuntimeException('Failed to make JS files injector.');
		}
		return $insert;
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