<?php

namespace BowerModule\ServiceRetriever;

/**
 * Service retriever declaration for writing "Aware" traits
 */
trait DeclareTrait 
{
	/**
	 * Returns service instance by its name
	 * @param string $serviceName
	 * @return mixed
	 */
	public abstract function retrieveService($serviceName);
} 