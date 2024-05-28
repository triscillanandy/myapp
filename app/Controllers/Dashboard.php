<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{

    public $session;

 

    public function dashboard()
    {
        // Check if user is logged in
        if (session()->has('logged_user') || session()->has('google_user')) {
            // Redirect to login page with an error message
            session()->setFlashdata("Error", "You are not logged in.");
            return redirect()->to(base_url("/"));
        }

        // Load necessary data for the dashboard
        $data = [];

        // Load header view
        echo view('templates/header', $data);

        // Load dashboard view
        echo view('dashboard');

        // Load footer view
        echo view('templates/footer');
    }
}
