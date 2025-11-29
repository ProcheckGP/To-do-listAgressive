<?php
class Controller
{
    protected function render($view, $data = [])
    {
        extract($data);
        require "view/{$view}.php";
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}
