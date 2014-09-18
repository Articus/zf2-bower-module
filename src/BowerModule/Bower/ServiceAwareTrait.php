<?php

namespace BowerModule\Bower;


use BowerModule\ServiceRetriever;

trait ServiceAwareTrait
{
	use ServiceRetriever\DeclareTrait;
	/**
	 * Returns bower service with strong type check
	 * @return Service
	 */
	public function getBower()
	{
		$service = $this->retrieveService(Service::class);
		if (!($service instanceof Service))
		{
			throw new \RuntimeException('Failed to retrieve '.Service::class.' service');
		}
		return $service;
	}

}

