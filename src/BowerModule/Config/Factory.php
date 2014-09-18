<?php

namespace BowerModule\Config;


use BowerModule\Config\Module as Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Factory implements FactoryInterface
{
	const CONFIG_KEY = 'bower';
	/**
	 * @inheritdoc
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$config = $serviceLocator->get('Config');
		$optionsConfig = isset($config[self::CONFIG_KEY]) ? $config[self::CONFIG_KEY] : [];
		$result = new Config($optionsConfig);
		return $result;
	}

} 