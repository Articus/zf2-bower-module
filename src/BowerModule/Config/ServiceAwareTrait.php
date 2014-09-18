<?php

namespace BowerModule\Config;

use BowerModule\ServiceRetriever;

trait ServiceAwareTrait
{
	use ServiceRetriever\DeclareTrait;

	/**
	 * Returns configuration service with strong type check
	 * @return Module
	 */
	public function getConfig()
	{
		$service = $this->retrieveService(Module::class);
		if (!($service instanceof Module))
		{
			throw new \RuntimeException('Failed to retrieve '.Module::class.' service');
		}
		return $service;
	}
} 