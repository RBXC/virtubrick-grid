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
}