<?php

declare(strict_types=1);

namespace Darkeum\Debugbar;

use Illuminate\Contracts\View\Engine;

class DebugbarViewEngine implements Engine
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var DarklyyDebugbar
     */
    protected $DarklyyDebugbar;

    /**
     * @param  Engine  $engine
     * @param  DarklyyDebugbar  $DarklyyDebugbar
     */
    public function __construct(Engine $engine, DarklyyDebugbar $DarklyyDebugbar)
    {
        $this->engine = $engine;
        $this->DarklyyDebugbar = $DarklyyDebugbar;
    }

    /**
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $shortPath = ltrim(str_replace(base_path(), '', realpath($path)), '/');

        return $this->DarklyyDebugbar->measure($shortPath, function () use ($path, $data) {
            return $this->engine->get($path, $data);
        });
    }

    /**
     * NOTE: This is done to support other Engine swap (example: Livewire).
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->engine->$name(...$arguments);
    }
}
