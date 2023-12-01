<?php

namespace App\Controllers;

class BaseController
{
    public function __construct($dbname, $dbhost, $dbuser, $dbpass)
    {
        connectToDatabase($dbname, $dbhost, $dbuser, $dbpass);
    }

    public function authorizeUser(): void
    {
        if (!isset($_SESSION['active_user'])) {
            header("Location:/user/login?id=2");
            die();
        }
    }
}