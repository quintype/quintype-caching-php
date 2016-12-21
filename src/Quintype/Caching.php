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

    private function buildSurrogateKey($cacheParams)
    {
        $surrogateKey = '';
        if (isset($cacheParams['locationId']) && isset($cacheParams['storyGroup'])) {
            $surrogateKey = 'sq/'.$cacheParams['publisherId'].'/'.$cacheParams['storyGroup'].'/'.$cacheParams['locationId'];
        }

        if (isset($cacheParams['storiesToCache'])) {
            $surrogateKey = $surrogateKey.' '.$this->buildStoryKeys($cacheParams);
        }

        return ['Surrogate-Key' => $surrogateKey];
    }

    public function buildCacheHeaders($cacheParams)
    {
        $browserTTLs = $cacheParams['browserTTLs'];
        $cdnTTLs = $cacheParams['cdnTTLs'];

        $commonHeaders = [
          'Cache-Control' => 'public,max-age='.$browserTTLs['max-age'],
          'Surrogate-Control' => 'public,max-age='.$cdnTTLs['max-age'].',stale-while-revalidate='.$cdnTTLs['stale-while-revalidate'].',stale-if-error='.$cdnTTLs['stale-if-error'],
          'Vary' => 'Accept-Encoding',
        ];

        return array_merge($commonHeaders, $this->buildSurrogateKey($cacheParams));
    }
}
