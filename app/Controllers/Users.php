<?php namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $helpers = ['form'];



    public function login()
    {
        if (!$this->request->getMethod() == 'post') {
            return view('login');
        }

        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Email or Password don\'t match'
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            $data['validation'] = $this->validator;
            return view('login', $data);
        }

       // $model = new UserModel();
       // $user = $model->where('email', $this->request->getVar('email'))->first();
        //$this->setUserSession($user);

        return redirect()->to('dashboard');
    }


    public function register()
    {
        if (! $this->request->is('post')) {
            return view('register');
        }
        
        // Define validation rules
        $rules = [
            'firstname' => 'required|min_length[3]|max_length[20]',
            'lastname' => 'required|min_length[3]|max_length[20]',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'matches[password]',
        ];

        // Get POST data
        $data = $this->request->getPost(array_keys($rules));

       //validate the data

        if (! $this->validateData($data, $rules)) {
            return view('register');
        }

        // If you want to get the validated data.
        $validData = $this->validator->getValidated();

        // Save the user to the database
        $userModel = new UserModel();

        $newUserData = [
            'firstname' => $this->request->getVar('firstname'),
            'lastname' => $this->request->getVar('lastname'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password')
        ];

        $userModel->save($newUserData);

        // Set a success message in session data
        $session = session();
        $session->setFlashdata('success', 'Successful Registration');

        // Redirect to the homepage or login page
        return redirect()->to('/login');
    }
}
