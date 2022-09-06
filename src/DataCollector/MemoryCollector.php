<?php

namespace Darkeum\Debugbar\DataCollector;

use DebugBar\DataCollector\MemoryCollector as DebugBarMemoryCollector;

class MemoryCollector extends DebugBarMemoryCollector
{
   
    public function getWidgets()
    {
        return array(
            "memory" => array(
                "icon" => "cogs",
                "tooltip" => "Использовано памяти",
                "map" => "memory.peak_usage_str",
                "default" => "'0B'"
            )
        );
    }
}
