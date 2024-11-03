<?php
/*
  Copyright (C) 2024 kylegg. All rights reserved.
*/

namespace Virtubrick\Grid\Rcc;

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
			// This generates a RFC 4122 compliant Version 4 UUID to be used as a default job ID.
			// https://www.rfc-editor.org/rfc/rfc4122#section-4.4
			// https://stackoverflow.com/a/15875555
			$guidv4 = random_bytes(16);
			$guidv4[6] = chr(ord($guidv4[6]) & 0x0f | 0x40); // Encode time_hi_and_version
			$guidv4[8] = chr(ord($guidv4[8]) & 0x3f | 0x80); // Encode clock_seq_hi_and_reserved
			
			$this->id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($guidv4), 4));
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
	
	public function execute(LuaScript $input): array
	{
		if(!$this->arbiter)
			throw new \Exception('Job has no arbiter associated.');
		
		$script = collect($input)->toArray();
		$script['arguments'] = GridService::serializeArray($input->arguments);
		
		return $this->arbiter->soapCall('ExecuteEx', array([
			'jobID' => $this->id,
			'script' => $script
		]));
	}
	
	public function renewLease(int $expiration): array
	{
		if(!$this->arbiter)
			throw new \Exception('Job has no arbiter associated.');
		
		return $this->arbiter->soapCall('RenewLease', array([
			'jobID' => $this->id,
			'expirationInSeconds' => $expiration
		]));
	}
	
	public function closeJob(): array
	{
		if(!$this->arbiter)
			throw new \Exception('Job has no arbiter associated.');
		
		return $this->arbiter->soapCall('CloseJob', array([
			'jobID' => $this->id
		]));
	}

	public function closeAllJobs(): array
	{
		if(!$this->arbiter)
			throw new \Exception('Job has no arbiter associated.');
		
		return $this->arbiter->soapCall('CloseAllJobs');
	}
}
