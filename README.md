# Virtubrick\Grid
This library allows for communication between the Virtubrick website and the Roblox Grid Service.

## Virtubrick\Grid\Rcc\Job
The Rcc\Job class is the first thing you should be constructing. All functions to interact with the Grid Service are called from here.

### Constructor
- `string $id` *(An RFC 4122 compliant Version 4 UUID will be substituted as the job ID if this is a blank string)*
- `int $expirationInSeconds` *(20)*
- `int $cores` (0)
- `int $category` (0)

### Methods
- `arbiter(Virtubrick\Grid\GridService): Job` Sets the arbiter that the Job will contact. Returns itself so these functions may be chained.
- `script(Virtubrick\Grid\Rcc\LuaScript): Job` Sets the script that the Job opens with. Returns itself so these functions may be chained.
- `batch(): array` Will batch the Job. Returns results from the lua script.
- `open(): array` Will open the Job. Returns results from the lua script.
- `execute(\Virtubrick\Grid\Rcc\LuaScript $input): array` Executes a script on the job. Returns result from the executed script.
- `renewLease(int $expiration): array` Renews the lease on the Job for $expiration amount of seconds.
- `closeJob(): array` Closes the job.
- `closeAllJobs(): array` Closes all present jobs.
## Virtubrick\Grid\Rcc\LuaScript
Class that allows easy communication for scripts via the Job class.

### Constructor
- `string $name`
- `string $script`
- `array $arguments` *(Empty array by default)*

## Virtubrick\Grid\GridService
This class describes your arbiter.

### Constructor
- `string $arbiter` *(ex. http://127.0.0.1:64989)*

### Methods
- `soapCall(string $name, ?array $args = null): array` Sends a soap call to the Arbiter service. See [RCCService.wsdl](Resources/RCCService.wsdl) for details.


## Example Usage:
Generating a Thumbnail
```php
use Virtubrick\Grid\GridService;
use Virtubrick\Grid\Rcc\{Job, LuaScript};

$job = (new Job($jobId = '', $expirationInSeconds = 120))
		->arbiter(new GridService('http://127.0.0.1:64989'))
		->script(new LuaScript(
			$name = "RenderThumbnail {$key}",
			$script = $script,
			$arguments = $arguments
		));

[$renderB64, $assetDependencies] = $job->batch();

header('x-asset-dependencies', implode(',', $assetDependencies));
exit(base64_decode($renderB64));
```

Copyright 2024 kylegg
