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

use Darkeum\Debugbar\Support\Clockwork\Converter;
use DebugBar\OpenHandler;
use Illuminate\Http\Response;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Storage\EntryQueryOptions;
use Laravel\Telescope\Telescope;

class TelescopeController extends BaseController
{
    public function show(EntriesRepository $storage, $uuid)
    {

        $entry = $storage->find($uuid);
        $result = $storage->get('request', (new EntryQueryOptions())->batchId($entry->batchId))->first();

        return redirect(config('telescope.path') . '/requests/' . $result->id);
    }
}
