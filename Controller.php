<?php

namespace mvc;
use mvc\View\TemplateView;

class Controller
{
    /**
     * @var array Die Model-Objekte (aus dir "Model") die by default geladen werden
     */
    public array $models = array();

    /**
     * @var array Parameter aus der url (/controller_name/action_name/[0]/[1]/...)
     */
    protected array $params = array();

    /**
     * Wird intern verwendet; nicht anfassen!
     * @param $val
     * @return void
     */
    public function setParam($val) : void
    {
        $this->params[] = $val;
    }

    /**
     * Zieht einen GET oder (falls nicht gefunden) POST parameter
     * @param string $name param name
     * @return string value
     */
    protected function getParam(string $name) : string
    {
        if (isset($_GET[$name])) return $_GET[$name];
        if (isset($_POST[$name])) return $_POST[$name];
        return "";
    }

    /**
     * Setzt return type auf json und encodet die eingabe als json
     * @param $json object
     * @return string json value
     */
    protected function json($json) :string
    {
        header("Content-Type: application/json");
        return json_encode($json);
    }

    /**
     * Setzt den Statuscode und gibt bei codes >= 300 eine Error Page zurück
     * @param int $code
     * @return string
     */
    protected function statuscode(int $code) : string
    {
        http_response_code($code);
        if ($code >= 300) {
            $view = new TemplateView("error-page");
            $view->setJsVar("statuscode", $code);
            $view->setContent("error_msg", "");
            return $view->generateHTMLValue();
        }
        return "";
    }

    /**
     * Läd die entsprechenden Model aus dem dir "Model"
     * @param string ...$name
     * @return void
     */
    protected function loadModel(string ...$name)
    {
        foreach ($name as $n) {
            require_once "Model/" . ucfirst($n) . "Model.php";
        }
    }
}