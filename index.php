<?php
/**
 * Copyright (full project) Jonathan Dreisvogt (jdreisvogt.de) | 2023
 */
use mvc\Config;
use mvc\Controller;
require_once "Config.php";
require_once "Model.php";
require_once "Controller.php";
require_once "View/TemplateView.php";

function error_handler($errno, $errstr, $errfile, $errline): void
{
    try {
        $view = new \mvc\View\TemplateView("error-page");
        $view->setJsVar("statuscode", 500);
        $view->setContent("error_msg", Config::$error_output ? "Fehler in $errfile:$errline<br>$errstr":"Fehlerausgabe ist deaktiviert");
        die($view->generateHTMLValue());
    } catch (Exception) {
        die("Critical internal error!! Unable to create detailed error message.");
    }
}

set_error_handler("error_handler");
if (Config::$maintenance) {
    $view = new \mvc\View\TemplateView("error-page");
    $view->setJsVar("statuscode", 503);
    $view->setContent("error_msg", "");
    die($view->generateHTMLValue());
}

function loadController(string $name) : Controller | null
{
    $filename = "Controller/" . $name . "Controller.php";
    if (!file_exists($filename)) return null;
    require $filename;
    try {

        $controller = null;
        $controller = new ("Controller\\" . $name . "Controller")();
        if ($controller == null) {
            throw new Exception();
        }
        foreach ($controller->models as $model_name) {
            require_once "Model/" . $model_name . "Model.php";
        }

        return $controller;

    } catch (Exception) {
        return null;
    }
}

function router($path) : string
{
    $path = explode("/", $path);
    if (count($path) <= 1 || empty($path[1])) {
        $controller = loadController("Default");
    }
    else{
        $controller = loadController(ucfirst($path[1]));
        if ($controller === null) {
            $controller = loadController("Default");
            setParams($path, 1, $controller);
        }
    }
    if (isset($path[2]) && method_exists($controller, ucfirst($path[2]) . "Action")) {
        setParams($path, 3, $controller);
        return $controller->{ucfirst($path[2]) . "Action"}();
    }

    setParams($path, 2, $controller);
    return $controller->IndexAction();
}

function setParams(array $array, int $start_index, Controller $controller): void
{
    $len = count($array);
    for ($i = $start_index; $i < $len; $i++) {
        $controller->setParam($array[$i]);
    }
}

if (Config::$use_param_url) {
    $c = ucfirst($_GET["c"] ?? "Default");
    $a = ucfirst($_GET["a"] ?? "Index") . "Action";
    $controller = loadController($c);
    if ($controller == null) $controller = loadController("Default");
    if (!method_exists($controller, $a)) $a = "IndexAction";
    echo $controller->{$a}();
} else {
    $requestUrl = str_replace("/zow/insta", "", $_SERVER['REQUEST_URI']);
    $beforeIndexPosition = strpos($_SERVER['PHP_SELF'], '/zow/insta/index.php');
    if (false !== $beforeIndexPosition && $beforeIndexPosition > 0) {
        $scriptUrl = substr($_SERVER['PHP_SELF'], 0, $beforeIndexPosition) . 'index.php/';
        $requestUrl = str_replace(['/index.php', $scriptUrl], '/', $requestUrl);
    }
    echo router($requestUrl);
}