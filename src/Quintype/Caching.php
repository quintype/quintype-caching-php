<?php

namespace Quintype\Caching;

class Caching
{
    private function buildStoryKeys($params)
    {
        $storyKeys = [];
        foreach ($params['storiesToCache'] as $story) {
            array_push($storyKeys, 'ss/'.$params['publisherId'].'/'.substr(trim($story['id']), 0, 8));
        }
        return implode(' ', $storyKeys);
    }

    private function buildCollectionKeys($params)
    {
        $collectionKeys = [];
        foreach ($params['storiesToCache'] as $key =>  $collection) {
          if (isset($collection['items']) && isset($collection['id'])) {
            array_push($collectionKeys, 'c/'.$params['publisherId'].'/'. trim($collection['id']));
          }
        }
        return implode(' ', $collectionKeys);
    }

    private function buildStoryKeysFromCollections($params)
    {
        $stories = [];
        foreach ($params['storiesToCache'] as $key => $collection) {
          if (isset($collection['items']) && sizeof($collection['items']) > 5 ) {
            foreach (array_slice($collection['items'], 0, 5) as $key => $item) {
              if ($item['type'] === 'story') {
                array_push($stories, $item['story']);
              }
            }
          }
        }
        $params['storiesToCache'] = $stories;
        return $this->buildStoryKeys($params);
    }

    private function buildSurrogateKey($cacheParams)
    {
        $surrogateKey = '';
        if (isset($cacheParams['locationId']) && isset($cacheParams['storyGroup'])) {
            $surrogateKey = 'sq/'.$cacheParams['publisherId'].'/'.$cacheParams['storyGroup'].'/'.$cacheParams['locationId'];
        }

        if (isset($cacheParams['storiesToCache'])) {
          if (isset($cacheParams['storiesFrom']) && $cacheParams['storiesFrom'] === "collection"){
            $surrogateKey = $surrogateKey.' '.$this->buildStoryKeysFromCollections($cacheParams).' '. $this->buildCollectionKeys($cacheParams);
          } else {
            $surrogateKey = $surrogateKey.' '.$this->buildStoryKeys($cacheParams);
          }
        }

        return [
            'Cache-Tag' => str_replace(" ", ",", $surrogateKey),
            'Surrogate-Key' => $surrogateKey
        ];
    }

    public function buildCacheHeaders($cacheParams)
    {
        if(sizeof($cacheParams) < 1){
          return ['Cache-Control' => 'private, no-cache'];
        }

        $browserTTLs = $cacheParams['browserTTLs'];
        $cdnTTLs = $cacheParams['cdnTTLs'];

        $commonHeaders = [
          'Cache-Control' => 'public,max-age='.$browserTTLs['max-age'].',s-maxage='.$cdnTTLs['max-age'].',stale-while-revalidate='.$cdnTTLs['stale-while-revalidate'].',stale-if-error='.$cdnTTLs['stale-if-error'],
          'Surrogate-Control' => 'public, max-age='.$cdnTTLs['max-age'].', stale-while-revalidate='.$cdnTTLs['stale-while-revalidate'].', stale-if-error='.$cdnTTLs['stale-if-error'],
          'Vary' => 'Accept-Encoding',
        ];

        return array_merge($commonHeaders, $this->buildSurrogateKey($cacheParams));
    }
}
