<?php
/*
  Copyright (C) 2024 kylegg. All rights reserved.
*/

namespace Virtubrick\Grid\Rcc;

class LuaScript
{
	public function __construct(public string $name, public string $script, public array $arguments = [])
	{
		//
	}
	
	public function toArray(): array
	{
		return (array) $this;
	}
}