<?php

/*
* @name        DARKLYY PHP Debug Bar
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Debugbar;

use Boot\App\Application;
use Boot\System\Routing\Router;
use Boot\Support\Collection;
use Boot\Contracts\Http\Kernel;
use Boot\System\Session\SessionManager;
use DebugBar\DataFormatter\DataFormatter;
use Illuminate\View\Engines\EngineResolver;
use Darkeum\Debugbar\Middleware\InjectDebugbar;
use DebugBar\DataFormatter\DataFormatterInterface;
use Boot\Support\ServiceProvider as ServiceProviderBase;

class ServiceProvider extends ServiceProviderBase
{
    /**
     * Регистрация сервис провайдера
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/debugbar.php';
        $this->mergeConfigFrom($configPath, 'debugbar');

        $this->app->alias(
            DataFormatter::class,
            DataFormatterInterface::class
        );

        $this->app->singleton(DarklyyDebugbar::class, function ($app) {
            $debugbar = new DarklyyDebugbar($app);

            if ($app->bound(SessionManager::class)) {
                $sessionManager = $app->make(SessionManager::class);
                $httpDriver = new SymfonyHttpDriver($sessionManager);
                $debugbar->setHttpDriver($httpDriver);
            }

            return $debugbar;
        });

        $this->app->alias(DarklyyDebugbar::class, 'debugbar');

        $this->app->singleton(
            'command.debugbar.clear',
            function ($app) {
                return new Console\ClearCommand($app['debugbar']);
            }
        );

        $this->app->extend(
            'view.engine.resolver',
            function (EngineResolver $resolver, Application $application): EngineResolver {
                $DarklyyDebugbar = $application->make(DarklyyDebugbar::class);

                $shouldTrackViewTime = $DarklyyDebugbar->isEnabled() &&
                    $DarklyyDebugbar->shouldCollect('time', true) &&
                    $DarklyyDebugbar->shouldCollect('views', true) &&
                    $application['config']->get('debugbar.options.views.timeline', false);

                if (! $shouldTrackViewTime) {
                    return $resolver;
                }

                return new class ($resolver, $DarklyyDebugbar) extends EngineResolver {
                    private $DarklyyDebugbar;

                    public function __construct(EngineResolver $resolver, DarklyyDebugbar $DarklyyDebugbar)
                    {
                        foreach ($resolver->resolvers as $engine => $resolver) {
                            $this->register($engine, $resolver);
                        }
                        $this->DarklyyDebugbar = $DarklyyDebugbar;
                    }

                    public function register($engine, \Closure $resolver)
                    {
                        parent::register($engine, function () use ($resolver) {
                            return new DebugbarViewEngine($resolver(), $this->DarklyyDebugbar);
                        });
                    }
                };
            }
        );

        Collection::macro('debug', function () {
            debug($this);
            return $this;
        });
      
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/debugbar.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        $this->loadRoutesFrom(realpath(__DIR__ . '/debugbar-routes.php'));

        $this->registerMiddleware(InjectDebugbar::class);

        if ($this->app->runningInConsole()) {
            $this->commands(['command.debugbar.clear']);
        }
    }

    /**
     * Получить маршруты
     *
     * @return Router
     */
    protected function getRouter()
    {
        return $this->app['router'];
    }

    /**
     * Получить путь до конфигарции
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('debugbar.php');
    }

    /**
     * Публикация файла конфигурации
     *
     * @param  string $configPath
     */
    protected function publishConfig($configPath)
    {
        $this->publishes([$configPath => config_path('debugbar.php')], 'config');
    }

    /**
     * Регситрация Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}
