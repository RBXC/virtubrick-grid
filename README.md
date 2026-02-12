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
- `toArray(): array` Get the job as an array, does not soapcall.
- `arbiter(Virtubrick\Grid\GridService): Job` Sets the arbiter that the Job will contact. Returns itself so these functions may be chained.
- `script(Virtubrick\Grid\Rcc\LuaScript): Job` Sets the script that the Job opens with. Returns itself so these functions may be chained.
- `batch(): array` Will batch the Job. Returns results from the lua script.
- `open(): array` Will open the Job. Returns results from the lua script.
- `getExpiration(): float` The amount of seconds until the job expires.
- `execute(\Virtubrick\Grid\Rcc\LuaScript $input): array` Executes a script on the job. Returns result from the executed script.
- `renewLease(int $expiration): array` Renews the lease on the Job for $expiration amount of seconds.
- `closeJob(): array` Closes the job.
- `diag(int $type): array` Collects diagnostic data about the job. (type: 0 = general diagnostic data; 1 = leak dump; 2 = allocation test; 3 = get duty cycles)
## Virtubrick\Grid\Rcc\LuaScript
Class that allows easy communication for scripts via the Job class.

### Constructor
- `string $name`
- `string $script`
- `array $arguments` *(Empty array by default)*

### Methods
- `toArray(): array` Get the job as an array, does not soapcall.
## Virtubrick\Grid\GridService
This class describes your arbiter.

### Constructor
- `string $arbiter` *(ex. http://127.0.0.1:64989)*

### Methods
- `soapCall(string $name, ?array $args = null): array` Sends a soap call to the Arbiter service. See [RCCService.wsdl](Resources/RCCService.wsdl) for details.
- `helloWorld(): string` "Hello World" Health check constant.
- `getVersion(): string` The current arbiter's version.
- `getStatus(): array` An object containing the version and job count of the current arbiter.
- `getAllJobs(): array` All open jobs.
- `closeExpiredJobs(): int` Closes all expired jobs and returns the count of jobs that have been closed.
- `closeAllJobs(): int` Closes all present jobs and returns the count of jobs that have been closed.

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
header('content-type', "image/png");
exit(base64_decode($renderB64));
```

Copyright 2024 kylegg
