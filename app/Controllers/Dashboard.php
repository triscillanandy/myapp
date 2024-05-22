<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {  
  
		$data = [];

		// echo view('templates/header', $data);
		// echo view('dashboard');
        return view('dashboard', $data);
    }
}
