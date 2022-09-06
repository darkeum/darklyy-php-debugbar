<?php

namespace Darkeum\Debugbar\DataCollector;

use Boot\App\Application;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\DataCollector;

class LaravelCollector extends DataCollector implements Renderable
{
    protected $app;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function collect()
    {
        // Fallback if not injected
        $app = $this->app ?: app();

        return [
            "version" => $app::VERSION,
            "environment" => $app->environment(),
            "locale" => $app->getLocale(),
        ];
    }

    public function getName()
    {
        return 'darklyy';
    }

    public function getWidgets()
    {
        return [
            "version" => [
                "icon" => "github",
                "tooltip" => "Версия Darklyy",
                "map" => "darklyy.version",
                "default" => ""
            ],
            "environment" => [
                "icon" => "desktop",
                "tooltip" => "Среда",
                "map" => "darklyy.environment",
                "default" => ""
            ],
            "locale" => [
                "icon" => "flag",
                "tooltip" => "Текущий язык",
                "map" => "darklyy.locale",
                "default" => "",
            ],
        ];
    }
}
