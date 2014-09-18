<?php

namespace BowerModule\PathBuilder;

use BowerModule\ServiceRetriever;

trait ServiceAwareTrait
{
	use ServiceRetriever\DeclareTrait;

	/**
	 * Returns path builder service with strong type check
	 * @return Service
	 */
	public function getPathBuilder()
	{
		$service = $this->retrieveService(Service::class);
		if (!($service instanceof Service))
		{
			throw new \RuntimeException('Failed to retrieve '.Service::class.' service');
		}
		return $service;
	}

} 