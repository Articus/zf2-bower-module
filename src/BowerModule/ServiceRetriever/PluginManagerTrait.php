<?php

namespace BowerModule\ServiceRetriever;


use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service retriever for classes that have access to PluginManager
 */
trait PluginManagerTrait
{
	use DeclareTrait;

	/**
	 * @return ServiceLocatorInterface
	 */
	abstract function getServiceLocator();

	/**
	 * Returns verified instance of PluginManager
	 * @return AbstractPluginManager
	 * @throws \RuntimeException
	 */
	public function getPluginManager()
	{
		$result = $this->getServiceLocator();
		if (!($result instanceof AbstractPluginManager))
		{
			throw new \RuntimeException('Failed to retrieve PluginManager.');
		}
		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function retrieveService($serviceName)
	{
		return $this->getPluginManager()->getServiceLocator()->get($serviceName);
	}
}