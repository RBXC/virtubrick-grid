<?php
/*
  Copyright (C) 2024 kylegg. All rights reserved.
*/

namespace Virtubrick\Grid;

use SoapClient;
use Virtubrick\Grid\Rcc\{Job, LuaScript};
use Virtubrick\Grid\Traits\GridSerializerTrait;

class GridService
{
	use GridSerializerTrait;
	
	private SoapClient $soapClient;
	
	public function __construct(private string $arbiter)
	{
		$this->soapClient = new SoapClient(
			dirname(__FILE__) . '/Resources/RCCService.wsdl',
			[
				'location' => $this->arbiter
			]
		);
	}
	
	public function soapCall(string $name, ?array $args = null): array
	{
		$result = $this->soapClient->__soapCall($name, $args ?? []);
		return static::deserializeArray($result);
	}

	public function helloWorld(): string
	{
		return $this->soapCall('HelloWorld')[0];
	}

	public function getVersion(): string
	{
		return $this->soapCall('GetVersion')[0];
	}

	public function getStatus(): array
	{
		return $this->soapCall('GetStatus');
	}

	public function getAllJobs(): array
	{
		return $this->soapCall('GetAllJobsEx');
	}

	public function closeExpiredJobs(): int
	{
		return $this->soapCall('CloseExpiredJobs')[0];
	}

	public function closeAllJobs(): int
	{
		return $this->soapCall('CloseAllJobs')[0];
	}
}