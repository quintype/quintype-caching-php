# quintype-caching-php
A composer package for caching for all quintype PHP projects.
All publisher websites should be cached to reduce load to the server.

###Important : If any change is made to the package, do the following.
* Create a new release.
* Update the package in [Packagist](https://packagist.org/).
* To use the new version of the package in any project, change the version number in composer.json file and run
`$ composer update `

###Implementation
Pass the required argument(array) to function **buildCacheHeaders()**, and pass the return value(array) as the argument to Laravel function **withHeaders()** on **response()**.
The argument is an associative array with following keys:
1. publisherId
2. cdnTTLs: Corresponds to Surrogate-Control values.
..1. max-age: This controls how long the page is considered fresh in the Database.
..2. stale-while-revalidate: During this period, the page is served from CDN, but updated in the background.
..3. stale-if-error: During this time, the page is served from CDN in case the backend server crashes for whatever reason.
3. browserTTLs: Corresponds to Cache-Control values
..1. max-age
4. locationId(not required for story page): home or section-id
5. storyGroup(not required for story page): top or stack-id
6. storiesToCache: Array of all the stories that have to be cached.
