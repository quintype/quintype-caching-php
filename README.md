# quintype-caching-php
A composer package for caching for all quintype PHP projects.
All publisher websites should be cached to reduce load to the server.

###Important : If any change is made to the package, do the following.
* Create a new release.
* Update the package in [Packagist](https://packagist.org/).
* To use the new version of the package in any project, change the version number in composer.json file and run
`$ composer update `

###Instructions to include the package into a project.

####  In composer.json, require Caching package.
```php
"require": {
        ...
        ...
        "quintype/caching":"1.0.0"
    },
```

####  Install or update the composer packages.
```sh
$ composer install
or
$ composer update
```

####  In the Laravel config/app.php file, add aliases to the packages for convenience.
```php
'aliases' => [
        ...
        ...
        'Caching' => Quintype\Caching\Caching::class
    ],
```

####  Include both Caching class in the necessary controllers.
```php
use Caching;
```

####  Create a constructor function to initialize the Caching object and to set default caching parameters..
```php
public function __construct(){
  $this->caching = new Caching();
  $this->defaultCacheParams = [
      'publisherId' => <publisher_id>,
      'cdnTTLs' => [
        'max-age' => 3 * 60,
        'stale-while-revalidate' => 5 * 60,
        'stale-if-error' => 4 * 60 * 60,
      ],
      'browserTTLs' => [
        'max-age' => 60,
      ],
  ];
}
```

####  Call the function using the created object. Pass all necessary details that will be used for setting cache headers to the function as an associative array.

The argument is an associative array with following keys:

1. **publisherId**
2. **cdnTTLs**: Corresponds to Surrogate-Control values.
  1. **max-age**: This controls how long the page is considered fresh in the Database.
  2. **stale-while-revalidate**: During this period, the page is served from CDN, but updated in the background.
  3. **stale-if-error**: During this time, the page is served from CDN in case the backend server crashes for whatever reason.
3. **browserTTLs**: Corresponds to Cache-Control values
  1. **max-age**
4. **locationId**_(not required for story page)_: home or section-id
5. **storyGroup**_(not required for story page)_: top or stack-id
6. **storiesToCache**:
  1. Array of all collections under a key for each of them, if driven through collections.
  2. Array of all the stories that have to be cached, if driven through stories.
7. **storiesFrom**_(required for collection driven page)_: collection


```php
return response(view("home_page", $this->toView([])))
        ->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ["locationId" => "home", "storyGroup" => "top", "storiesToCache" => $storiesToCache])));

return response(view("section_page", $this->toView([])))
        ->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ["locationId" => $params["section-id"], "storyGroup" => $params["story-group"], "storiesToCache" => $storiesToCache])));

return response(view("story_page", $this->toView([])))
        ->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ["storiesToCache" => $storiesToCache])));

return response(view("collection_page", $this->toView([])))
        ->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ["locationId" => "<depends_on_the_page>", "storiesFrom"=> "collection", "storiesToCache" => $storiesToCache])));

```
