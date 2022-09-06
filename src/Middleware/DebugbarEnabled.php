<?php

namespace Darkeum\Debugbar\Middleware;

use Closure;
use Boot\System\Http\Request;
use Darkeum\Debugbar\DarklyyDebugbar;

class DebugbarEnabled
{
    /**
     * The DebugBar instance
     *
     * @var DarklyyDebugbar
     */
    protected $debugbar;

    /**
     * Create a new middleware instance.
     *
     * @param  DarklyyDebugbar $debugbar
     */
    public function __construct(DarklyyDebugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->debugbar->isEnabled()) {
            abort(404);
        }

        return $next($request);
    }
}
