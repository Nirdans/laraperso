<?php

namespace App\Core;

class View
{
    public static function render($template, $data = [])
    {
        $templateFile = BASE_PATH . '/views/' . ltrim($template, '/') . '.php';

        if (!file_exists($templateFile)) {
            throw new \Exception("Template not found: {$template}");
        }

        extract($data);
        
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    public static function include($partial, $data = [])
    {
        extract($data);
        include BASE_PATH . '/views/' . ltrim($partial, '/') . '.php';
    }
}
