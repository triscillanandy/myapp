<?php namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    
    //protected $helpers = ['url', 'form'];
    private $userModel = NULL;
    private $googleClient = NULL;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->userModel = new UserModel();
        $this->googleClient = new \Google\Client();
        $this->googleClient->setClientId("47846670195-a1g31504etm2lflsnga14ohft9ib98rf.apps.googleusercontent.com");
        $this->googleClient->setClientSecret("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");
        $this->googleClient->setRedirectUri("http://localhost/myapp/public/loginWithGoogle");
        $this->googleClient->addScope("email");
        $this->googleClient->addScope("profile");
    }
 
    

    

    public function index()
    {
        if (session()->get("LoggedUserData")) {
            session()->setFlashdata("Error", "You have Already Logged In");
            return redirect()->to(base_url("/profile"));
        }
        $data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'" ><img src="'.base_url('assests/uploads/google.png').'" alt="Login With Google" width="100%"></a>';
        return view('login', $data);
    }

    

    public function login()
    {
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
    
        if (!$validation) {
            return view('login', ['validation' => $this->validator]);
        } else {
           
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            $userModel = new UserModel();
    
            $userInfo = $userModel->where('email', $email)->first();
    
            $checkPassword = password_verify($password, $userInfo['password']);
            if (!$checkPassword) {
                session()->setFlashdata('fail', 'Incorrect password');
                return redirect()->to('login')->withInput();
            } elseif ($userInfo['status'] != 1) {
                // If user status is not activated, redirect to login with error message
                session()->setFlashdata('fail', 'Account not activated. Please check your email for activation link.');
                return redirect()->to('login')->withInput();
            } else {
                // User is activated, proceed with login
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


    public function loginWithGoogle()
    {
        if ($this->request->getVar('code')) {
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
            if (!isset($token['error'])) {
                $this->googleClient->setAccessToken($token['access_token']);
                session()->set("AccessToken", $token['access_token']);

                $googleService = new \Google\Service\Oauth2($this->googleClient);
                $data = $googleService->userinfo->get();
                $currentDateTime = date("Y-m-d H:i:s");

                $userdata = [];
                if ($this->userModel->isAlreadyRegister($data['email'])) {
                    // User Already Registered and wants to Login Again
                    $userdata = [
                     
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'updated_at' => $currentDateTime
                    ];
                    $this->userModel->updateUserData($userdata, $data['email']);
                } else {
                    // New User wants to Login
                    $userdata = [
                      
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'created_at' => $currentDateTime
                    ];
                    $this->userModel->insertUserData($userdata);
                }
                session()->set("LoggedUserData", $userdata);
            } else {
                session()->setFlashdata("Error", "Something went wrong");
                return redirect()->to(base_url());
            }
            // Successful Login
            return redirect()->to(base_url("/profile"));
        } else {
            session()->setFlashdata("Error", "Something went wrong");
            return redirect()->to(base_url());
        }
    }
    // public function register()
    // {
    //     if (! $this->request->is('post')) {
    //         return view('register');
    //     }
        
    //     // Define validation rules
    //     $rules = [
    //         'firstname' => 'required|min_length[3]|max_length[20]',
    //         'lastname' => 'required|min_length[3]|max_length[20]',
    //         'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
    //         'password' => 'required|min_length[8]|max_length[255]',
    //         'password_confirm' => 'matches[password]',
    //     ];

    //     // Get POST data
    //     $data = $this->request->getPost(array_keys($rules));

    //    //validate the data

    //     if (! $this->validateData($data, $rules)) {
    //         return view('register');
    //     }

    //     // If you want to get the validated data.
    //    // $validData = $this->validator->getValidated();

    //     // Save the user to the database
    //     $userModel = new UserModel();

    //     $newUserData = [
    //         'firstname' => $this->request->getVar('firstname'),
    //         'lastname' => $this->request->getVar('lastname'),
    //         'email' => $this->request->getVar('email'),
    //         'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
    //     ];

    //     $userModel->save($newUserData);

    //     // Set a success message in session data
    //     $session = session();
    //     $session->setFlashdata('success', 'Successful Registration');

    //     // Redirect to the homepage or login page
    //     return redirect()->to('/login');
    // }
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

        // Validate the data
        if (! $this->validateData($data, $rules)) {
            return view('register');
        }

        // Generate simple random code
        $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = substr(str_shuffle($set), 0, 12);

        // Prepare new user data
        $newUserData = [
            'firstname' => $this->request->getVar('firstname'),
            'lastname' => $this->request->getVar('lastname'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'code' => $code,
            'status' => 0, // Initially inactive
        ];

        // Save the user to the database
        $userModel = new UserModel();
        $userModel->save($newUserData);
        $userId = $userModel->getInsertID();

        // Send verification email
        $this->sendVerificationEmail($this->request->getVar('email'), $userId, $code);

        // Set a success message in session data
        $session = session();
        $session->setFlashdata('success', 'Successful Registration. Please check your email to activate your account.');

        // Redirect to the homepage or login page
        return redirect()->to('/login');
    }

    private function sendVerificationEmail($recipientEmail, $userId, $code)
    {
        $message = "
            <html>
            <head>
                <title>Verification Code</title>
            </head>
            <body>
                <h2>Thank you for Registering.</h2>
                <p>Please click the link below to activate your account.</p>
                <h4><a href='".base_url()."users/activate/".$userId."/".$code."'>Activate My Account</a></h4>
            </body>
            </html>
        ";

        $emailService = \Config\Services::email();
      
        $config['protocol'] = 'smtp';
        $config['SMTPHost'] = 'smtp.gmail.com';
        $config['SMTPUser'] = 'uprint332@gmail.com';
        $config['SMTPPass'] = 'vhklocvwhgyhtydk';
        $config['SMTPPort'] = 465;
        $config['mailType'] = 'html'; // Set email format to HTML
        $config['charset']  = 'utf-8'; // Set charset
        $config['wordWrap'] = true; 

       
        $emailService->initialize($config);
        
        $emailService->setFrom('uprint332@gmail.com', 'maria');
        $emailService->setTo($recipientEmail);
        $emailService->setSubject('Signup Verification Email');
        $emailService->setMessage($message);
        
        
        if ($emailService->send()) {
            session()->setFlashdata('message', 'Activation code sent to email');
        } else {
            $debugMessage = $emailService->printDebugger(['headers']);
            log_message('error', $debugMessage);
            session()->setFlashdata('message', 'Failed to send activation email: ' . $debugMessage);
        }
    }

    public function activate($id, $code)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        // If code matches
        if ($user && $user['code'] == $code) {
            // Update user status
            $data['status'] = 1;
            $data['code'] = null;

            if ($userModel->update($id, $data)) {
                session()->setFlashdata('success', 'User activated successfully');
            } else {
                session()->setFlashdata('error', 'Something went wrong in activating account');
            }
        } else {
            session()->setFlashdata('error', 'Cannot activate account. Code did not match');
        }

        return redirect()->to('/login');
    }

    // public function profile($id)
    // {
    //     $user = new UserModel();
    //     // $id = session()->get('id');
    //     $data['user']=  $user->find($id);
    //     echo view('templates/header');
    //     return view('profile',$data);
    //     echo view('templates/footer');

    // }

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
    public function forgotpassword()
    {
        if (! $this->request->is('post')) {
         
           // return  view('templates/header');
            return view('forgotpassword');
            //return view('templates/footer'); // Assuming you have a view for changing password
        }
         
        // Define validation rules
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|min_length[8]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]',
        ];
    
        // Get POST data
        $data = $this->request->getPost(array_keys($rules));
    
        // Validate the data
        if (! $this->validate($rules)) {
        
            return view('forgotpassword');
         
        }
    
        // Check if old password matches with the one in the database
        $userModel = new UserModel();
        $user = $userModel->where('email', session()->get('email'))->first();
    
        if (!password_verify($data['old_password'], $user['password'])) {
            // Old password does not match
            return redirect()->back()->withInput()->with('error', 'Old password is incorrect');
        }
    
        // Update the password
        $newPasswordHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
        $userModel->update($user['id'], ['password' => $newPasswordHash]);
    
        // Set a success message in session data
        session()->setFlashdata('success', 'Password updated successfully');
    
        // Return the view with the success message
        return view('forgotpassword');
        
    }
    

	
    // public function logout() {
    //     $session = session();
    //     $session->destroy();
        
      
    //    return redirect()->to('/login');
    
    
	
}


