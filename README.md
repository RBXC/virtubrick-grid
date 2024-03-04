# Virtubrick\Grid
This library allows for communication between the Virtubrick website and the Roblox Grid Service.

### Example Usage:
Generating a Thumbnail
```php
$job = (new Job($jobId = Str::uuid(), $expirationInSeconds = 120))
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

Copyright (C) 2024 kylegg. All rights reserved.