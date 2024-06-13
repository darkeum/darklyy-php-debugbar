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

use Darkeum\Debugbar\DarklyyDebugbar;
use Illuminate\Routing\Controller;
use Boot\System\Http\Request;
use Laravel\Telescope\Telescope;

// phpcs:ignoreFile
if (class_exists('Illuminate\Routing\Controller')) {

    class BaseController extends Controller
    {
        protected $debugbar;

        public function __construct(Request $request, DarklyyDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;

            if ($request->hasSession()) {
                $request->session()->reflash();
            }

            $this->middleware(function ($request, $next) {
                if (class_exists(Telescope::class)) {
                    Telescope::stopRecording();
                }
                return $next($request);
            });
        }
    }

} else {

    class BaseController
    {
        protected $debugbar;

        public function __construct(Request $request, DarklyyDebugbar $debugbar)
        {
            $this->debugbar = $debugbar;

            if ($request->hasSession()) {
                $request->session()->reflash();
            }
        }
    }
}
