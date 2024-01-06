<?php
/*
  Copyright (C) 2024 kylegg. All rights reserved.
*/

namespace Virtubrick\Grid\Rcc;

use Illuminate\Support\Str;
use Virtubrick\Grid\GridService;
use Virtubrick\Grid\Rcc\LuaScript;

class Job
{
	public ?GridService $arbiter = null;
	public ?LuaScript $script = null;
	
	public function __construct(public string $id = '', public int $expirationInSeconds = 20, public int $cores = 0, public int $category = 0)
	{
		if(empty($this->id))
		{
			$this->id = Str::uuid();
		}
	}
	
	public function arbiter(GridService $arbiter): Job
	{
		$this->arbiter = $arbiter;
		return $this;
	}
	
	public function script(LuaScript $script): Job
	{
		$this->script = $script;
		return $this;
	}
	
	public function open(): array
	{
		if(!$this->arbiter)
			throw new \Exception('Job has no arbiter associated.');
		
		if(!$this->script)
			throw new \Exception('Job has no script associated.');
		
		$script = collect($this->script)->toArray();
		$script['arguments'] = GridService::serializeArray($this->script->arguments);
		
		return $this->arbiter->soapCall('OpenJobEx', array([
			'job' => collect($this)->only(['id', 'expirationInSeconds', 'cores', 'category'])->toArray(),
			'script' => $script
		]));
	}
	
	public function batch(): array
	{
		if(!$this->arbiter)
			throw new \Exception('Job has no arbiter associated.');
		
		if(!$this->script)
			throw new \Exception('Job has no script associated.');
		
		$script = collect($this->script)->toArray();
		$script['arguments'] = GridService::serializeArray($this->script->arguments);
		
		return $this->arbiter->soapCall('BatchJobEx', array([
			'job' => collect($this)->only(['id', 'expirationInSeconds', 'cores', 'category'])->toArray(),
			'script' => $script
		]));
	}
}