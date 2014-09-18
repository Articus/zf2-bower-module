<?php

namespace BowerModule;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature as MMF;
use Zend\Config\Factory as ConfigFactory;

class Module implements
	MMF\ConfigProviderInterface,
	MMF\ViewHelperProviderInterface,
	MMF\ServiceProviderInterface,
	MMF\ConsoleUsageProviderInterface
{
	/**
	 * @inheritdoc
	 */
	public function getConfig()
	{
		return ConfigFactory::fromFile(__DIR__ . '/../../config/module.config.php');
	}

	/**
	 * @inheritdoc
	 */
	public function getViewHelperConfig()
	{
		return ConfigFactory::fromFile(__DIR__ . '/../../config/viewhelper.config.php');
	}

	/**
	 * @inheritdoc
	 */
	public function getServiceConfig()
	{
		return ConfigFactory::fromFile(__DIR__ . '/../../config/service.config.php');
	}


	/**
	 * @inheritdoc
	 */
	public function getConsoleUsage(AdapterInterface $console)
	{
		return [
			'bower prepare-packs' => 'Generates JS content for pack and debug folders',
		];
	}

}
