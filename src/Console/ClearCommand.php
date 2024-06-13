<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Darkeum\Debugbar\Console;

use DebugBar\DebugBar;
use Boot\System\Console\Command;

class ClearCommand extends Command
{
    protected $name = 'debugbar:clear';
    protected $description = 'Очистка хранилища Debugbar';
    protected $debugbar;

    public function __construct(DebugBar $debugbar)
    {
        $this->debugbar = $debugbar;

        parent::__construct();
    }

    public function handle()
    {
        $this->debugbar->boot();

        if ($storage = $this->debugbar->getStorage()) {
            try {
                $storage->clear();
            } catch (\InvalidArgumentException $e) {
                // hide InvalidArgumentException if storage location does not exist
                if (strpos($e->getMessage(), 'does not exist') === false) {
                    throw $e;
                }
            }
            $this->info('Хранилище Debugbar успешно очищено!');
        } else {
            $this->error('Хранилище Debugbar не найдено..');
        }
    }
}
