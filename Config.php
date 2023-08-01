<?php

namespace mvc;


class Config
{
    /**
     * @var bool
     * true -> url der form /?c=controller_name&a=action_name
     * false -> url der form /controller_name/action_name
     */
    static bool $use_param_url = true;

    /**
     * @var bool
     * true → fehler werden auf der error page ausgegeben
     */
    static bool $error_output = false;

    /**
     * @var bool
     * true → es wird unabhängig von der eingabe die Seite "Wartung" angezeigt
     */
    static bool $maintenance = false;
    static string $database_host = "localhost";
    static int $database_port = 3306;
    static string $database_name = ""; //todo
    static string $database_user = "root";
    static string $database_pw = "";


}