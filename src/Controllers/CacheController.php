<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Debugbar\Controllers;

class CacheController extends BaseController
{
    /**
     * Forget a cache key
     *
     */
    public function delete($key, $tags = '')
    {
        $cache = app('cache');

        if (!empty($tags)) {
            $tags = json_decode($tags, true);
            $cache = $cache->tags($tags);
        } else {
            unset($tags);
        }

        $success = $cache->forget($key);

        return response()->json(compact('success'));
    }
}
