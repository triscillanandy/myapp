<?php namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    //protected $helpers = ['url', 'form'];

    public function __construct()
    {
        helper(['url', 'form']);
    }
     
    public function login() {

        
        $validation = $this->validate([
            'email' => [
                'rules' => 'required|valid_email|is_not_unique[users.email]',
                'errors' => [
                    'required' => "Email Field Required",
                    'valid_email' => "Not a valid email",
                    'is_not_unique' => "Email not registered",
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => "Password Field Required"
                ]
            ],
        ]);
        if(!$validation) {
            return view('login', ['validation' => $this->validator]);
        } else {
            $session = session();
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            $userModel = new UserModel();

            $userInfo = $userModel->where('email', $email)->first();

            $checkPassword = password_verify($password, $userInfo['password']);
            if(!$checkPassword) {
                session()->setFlashdata('fail', 'Incorrect password');
                return redirect()->to('login')->withInput();
            } else {

                
                
                // $loggedUserId = $userInfo['id'];
                // $loggedUserFullName = $userInfo['firstname'].' '.$userInfo['lastname'];
    
                // session()->set('loggedUserId' , $loggedUserId);
                // session()->set('loggedUserFullName' , $loggedUserFullName);
                $this->setUserSession($userInfo);
                session()->setFlashdata('success', 'Login success');
                return redirect()->to('dashboard')->withInput();
            }
        }
    }


    
	private function setUserSession($userInfo){
        
		$data = [
			'id' => $userInfo['id'],
			'firstname' => $userInfo['firstname'],
			'lastname' => $userInfo['lastname'],
			'email' => $userInfo['email'],
			'isLoggedIn' => true,
		];

		session()->set($data);
		return true;
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
       // $validData = $this->validator->getValidated();

        // Save the user to the database
        $userModel = new UserModel();

        $newUserData = [
            'firstname' => $this->request->getVar('firstname'),
            'lastname' => $this->request->getVar('lastname'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
        ];

        $userModel->save($newUserData);

        // Set a success message in session data
        $session = session();
        $session->setFlashdata('success', 'Successful Registration');

        // Redirect to the homepage or login page
        return redirect()->to('/login');
    }


    public function profile($id)
    {
        $user = new UserModel();
        // $id = session()->get('id');
        $data['user']=  $user->find($id);
        echo view('templates/header');
        return view('profile',$data);
        echo view('templates/footer');

    }

    public function update($id)
    {
    $user = new UserModel();
    $data = [
        'firstname' => $this->request->getPost("firstname"),
        'lastname' => $this->request->getPost("lastname"),
        'email' => $this->request->getPost('email'),

    ];

    $user->update($id, $data);
    return redirect()->to(base_url("dashboard"))->with("status", " Updated Successfully");
}

    //     if ($this->request->getMethod() == 'post') {
    //         $rules = [
    //             'firstname' => 'required|min_length[3]|max_length[20]',
    //             'lastname' => 'required|min_length[3]|max_length[20]',
    //         ];

    //         if ($this->request->getPost('password') != '') {
    //             $rules['password'] = 'required|min_length[8]|max_length[255]';
    //             $rules['password_confirm'] = 'matches[password]';
    //         }

    //         if (!$this->validate($rules)) {
    //             return view('profile', [
    //                 'user' => $user,
    //                 'validation' => $this->validator
    //             ]);
    //         } else {
    //             $newData = [
    //                 'id' => $id,
    //                 'firstname' => $this->request->getPost('firstname'),
    //                 'lastname' => $this->request->getPost('lastname'),
    //             ];

    //             if ($this->request->getPost('password') != '') {
    //                 $newData['password'] =password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
    //             }

    //             $model->update($id, $newData);
    //             session()->setFlashdata('success', 'Successfully Updated');
    //             return redirect()->to('/profile');
    //         }
    //     }
    //     echo view('templates/header');
    //     return view('profile', [
    //         'user' => $user
    //     ]);
    //     echo view('templates/footer');
    // }
	
    public function logout() {
        $session = session();
        $session->destroy();
        
      
        return redirect()->to('/login');
    }
    
	
}
