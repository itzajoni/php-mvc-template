<?php

namespace mvc;

use Controller\DefaultController;
use Exception;
use PDO;
use PDOStatement;

class Model
{
    public bool $connected = false;
    public PDO $conn;

    protected function connectToDatabase(): void
    {
        if ($this->connected) return;
        try {
            $this->conn = new PDO("mysql:host=" . Config::$database_host . ":" . Config::$database_port . ";dbname="
                . Config::$database_name . ";charset=utf8", Config::$database_user, Config::$database_pw);
            $this->connected = true;
        } catch (Exception) {
            $view = new \mvc\View\TemplateView("error-page");
            $view->setJsVar("statuscode", 501);
            $view->setContent("error_msg", "");
            die($view->generateHTMLValue());
        }
    }

    protected function conn(string $sql): false|PDOStatement
    {
        return $this->conn->prepare($sql);
    }

    protected function query(string $query, $params = []):array
    {
        $req = $this->conn->prepare($query);
        for ($i = 1; $i <= count($params); $i++) {
            $req->bindValue($i, $params[$i - 1]);
        }
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}