<?php
namespace Controller;
use mvc\Controller;

/**
 * Der Default Controller, der bei fehlenden oder ungültigen Parametern aufgerufen wird
 */
class DefaultController extends Controller
{
    public function IndexAction()
    {
        return $this->statuscode(503);
        //header("Location: /?c=index"); //todo
    }
}