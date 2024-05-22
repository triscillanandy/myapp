<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        echo "Hello : ".$session->get('firstname');
    }
}
