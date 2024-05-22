<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Start the session
        $session = session();

        // Retrieve the logged-in user ID from the session
        $loggedInUserId = $session->get('id');

        // Ensure the user is logged in
        if (!$loggedInUserId) {
            // Redirect to login page if not logged in
            return redirect()->to('/login');
        }

        // Fetch user information from the database
        $userModel = new UserModel();
        $userInfo = $userModel->find($loggedInUserId);

        // Check if the user info was retrieved
        if (!$userInfo) {
            // Handle the case where the user does not exist
            return redirect()->to('/login')->with('fail', 'User not found');
        }

        // Prepare data for the view
        $data = [
            'id' => $userInfo['id'],
            'firstname' => $userInfo['firstname'],
            'lastname' => $userInfo['lastname'],
            'email' => $userInfo['email']
        ];

        echo view('templates/header', $data);
		echo view('dashboard');
		echo view('templates/footer');
    }
}