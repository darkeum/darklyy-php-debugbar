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

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

class JavascriptRenderer extends BaseJavascriptRenderer
{   
    protected $ajaxHandlerBindToJquery = false;
    protected $ajaxHandlerBindToXHR = true;

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $this->cssFiles['darklyy'] = __DIR__ . '/Resources/darklyy-debugbar.css';
        $this->cssVendors['fontawesome'] = __DIR__ . '/Resources/vendor/font-awesome/style.css';
        $this->jsFiles['darklyy-sql'] = __DIR__ . '/Resources/sqlqueries/widget.js';
        $this->jsFiles['darklyy-cache'] = __DIR__ . '/Resources/cache/widget.js';

        $theme = config('debugbar.theme', 'auto');
        switch ($theme) {
            case 'dark':
                $this->cssFiles['darklyy-dark'] = __DIR__ . '/Resources/darklyy-debugbar-dark-mode.css';
                break;
            case 'auto':
                $this->cssFiles['darklyy-dark-0'] = __DIR__ . '/Resources/darklyy-debugbar-dark-mode-media-start.css';
                $this->cssFiles['darklyy-dark-1'] = __DIR__ . '/Resources/darklyy-debugbar-dark-mode.css';
                $this->cssFiles['darklyy-dark-2'] = __DIR__ . '/Resources/darklyy-debugbar-dark-mode-media-end.css';
        }
    }

    /**
     * Set the URL Generator
     *
     * @param \Boot\System\Routing\UrlGenerator $url
     * @deprecated
     */
    public function setUrlGenerator($url)
    {
    }

    public function renderHead()
    {
        $cssRoute = route('debugbar.assets.css', [
            'v' => $this->getModifiedTime('css'),
            'theme' => config('debugbar.theme', 'auto'),
        ]);

        $jsRoute = route('debugbar.assets.js', [
            'v' => $this->getModifiedTime('js')
        ]);

        $cssRoute = preg_replace('/\Ahttps?:/', '', $cssRoute);
        $jsRoute  = preg_replace('/\Ahttps?:/', '', $jsRoute);

        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}' data-turbolinks-eval='false' data-turbo-eval='false'>";
        $html .= "<script src='{$jsRoute}' data-turbolinks-eval='false' data-turbo-eval='false'></script>";

        if ($this->isJqueryNoConflictEnabled()) {
            $html .= '<script data-turbo-eval="false">jQuery.noConflict(true);</script>' . "\n";
        }

        $html .= $this->getInlineHtml();


        return $html;
    }

    protected function getInlineHtml()
    {
        $html = '';

        foreach (['head', 'css', 'js'] as $asset) {
            foreach ($this->getAssets('inline_' . $asset) as $item) {
                $html .= $item . "\n";
            }
        }

        return $html;
    }
    /**
     * Get the last modified time of any assets.
     *
     * @param string $type 'js' or 'css'
     * @return int
     */
    protected function getModifiedTime($type)
    {
        $files = $this->getAssets($type);

        $latest = 0;
        foreach ($files as $file) {
            $mtime = filemtime($file);
            if ($mtime > $latest) {
                $latest = $mtime;
            }
        }
        return $latest;
    }

    /**
     * Return assets as a string
     *
     * @param string $type 'js' or 'css'
     * @return string
     */
    public function dumpAssetsToString($type)
    {
        $files = $this->getAssets($type);

        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }

        return $content;
    }

    /**
     * Makes a URI relative to another
     *
     * @param string|array $uri
     * @param string $root
     * @return string
     */
    protected function makeUriRelativeTo($uri, $root)
    {
        if (!$root) {
            return $uri;
        }

        if (is_array($uri)) {
            $uris = [];
            foreach ($uri as $u) {
                $uris[] = $this->makeUriRelativeTo($u, $root);
            }
            return $uris;
        }

        if (substr($uri ?? '', 0, 1) === '/' || preg_match('/^([a-zA-Z]+:\/\/|[a-zA-Z]:\/|[a-zA-Z]:\\\)/', $uri ?? '')) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }
}
