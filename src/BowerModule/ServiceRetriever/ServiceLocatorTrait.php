<?php

namespace BowerModule\ServiceRetriever;


use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service retriever for classes that have access to ServiceLocator
 */
trait ServiceLocatorTrait
{
	use DeclareTrait;

	/**
	 * @return ServiceLocatorInterface
	 */
	abstract function getServiceLocator();

	/**
	 * @inheritdoc
	 */
	public function retrieveService($serviceName)
	{
		return $this->getServiceLocator()->get($serviceName);
	}
}