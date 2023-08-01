<?php

namespace mvc\View;

class TemplateView
{
    private bool $loaded = false;
    private array $js_vars = array();
    private string $content;


    public function __construct(string $view_name)
    {
        $path = "View/html-content/" . $view_name . ".template.html";
        if (!file_exists($path)) return;
        $this->content = file_get_contents($path);
        $this->loaded = true;
    }

    public function setContent(string $placeholder, string $value): void
    {
        if (!$this->loaded) return;
        $this->content = preg_replace("/##" . $placeholder . "##/", $value, $this->content);
    }

    public function setSubView(string $placeholder, TemplateView $view)
    {
        if (!$this->loaded) return;
        $this->content = preg_replace("/##" . $placeholder . "##/", $view->generateHTMLValue(), $this->content);
    }

    public function setJsVar(string $name, int|string|array $val): void
    {
        $this->js_vars[$name] = $val;
    }

    public function generateHTMLValue() : string
    {
        if (!$this->loaded) return "";

        $json = json_encode($this->js_vars);
        $this->content = preg_replace("[<head>]", <<<HTML
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    window.server_params = JSON.parse('$json')
</script>
HTML, $this->content);
        return $this->content;
    }
}