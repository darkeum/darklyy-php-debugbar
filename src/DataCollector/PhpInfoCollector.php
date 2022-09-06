<?php

namespace Darkeum\Debugbar\DataCollector;

use DebugBar\DataCollector\PhpInfoCollector as DebugBarPhpInfoCollector;

class PhpInfoCollector extends DebugBarPhpInfoCollector
{
    public function getWidgets()
    {
        return tap(parent::getWidgets(), function (&$widgets) {
            data_set($widgets, 'php_version.tooltip', 'Версия PHP');
        });
    }
}
