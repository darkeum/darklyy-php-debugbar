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
use Boot\System\Http\Request;
use Boot\System\Http\Response;

class OpenHandlerController extends BaseController
{
    public function handle(Request $request)
    {
        $openHandler = new OpenHandler($this->debugbar);
        $data = $openHandler->handle($request->input(), false, false);

        return new Response(
            $data,
            200,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Return Clockwork output
     *
     * @param $id
     * @return mixed
     * @throws \DebugBar\DebugBarException
     */
    public function clockwork($id)
    {
        $request = [
            'op' => 'get',
            'id' => $id,
        ];

        $openHandler = new OpenHandler($this->debugbar);
        $data = $openHandler->handle($request, false, false);

        // Convert to Clockwork
        $converter = new Converter();
        $output = $converter->convert(json_decode($data, true));

        return response()->json($output);
    }
}
