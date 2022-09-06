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

use DebugBar\HttpDriverInterface;

class SymfonyHttpDriver implements HttpDriverInterface
{
    protected $session;

    protected $response;

    public function __construct($session, $response = null)
    {
        $this->session = $session;
        $this->response = $response;
    }

    public function setHeaders(array $headers)
    {
        if (!is_null($this->response)) {
            $this->response->headers->add($headers);
        }
    }

    public function isSessionStarted()
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->isStarted();
    }

    public function setSessionValue($name, $value)
    {
        $this->session->put($name, $value);
    }

    public function hasSessionValue($name)
    {
        return $this->session->has($name);
    }

    public function getSessionValue($name)
    {
        return $this->session->get($name);
    }

    public function deleteSessionValue($name)
    {
        $this->session->remove($name);
    }
}
