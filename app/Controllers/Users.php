<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ContactModel;

use \Firebase\JWT\JWT;

use CodeIgniter\API\ResponseTrait;
class Users extends BaseController
{
    use ResponseTrait;
    //protected $helpers = ['url', 'form'];
    public $userModel = NULL;
    private $googleClient = NULL;
    public $session;

    function __construct(){
        helper(['url', 'form','cookie']);
        $this->userModel = new UserModel();
        $this->session = session();
        
        $this->googleClient = new \Google\Client();
        $this->googleClient->setClientId("47846670195-a1g31504etm2lflsnga14ohft9ib98rf.apps.googleusercontent.com");
        $this->googleClient->setClientSecret("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");
        $this->googleClient->setRedirectUri("http://localhost/myapp/public/loginWithGoogle");
        $this->googleClient->addScope("email");
        $this->googleClient->addScope("profile");
    }

    // Check if user is logged in
   
    public function index()
    {
        if ($this->session->get("logged_user")) {
            session()->setFlashdata("Error", "You have Already Logged In");
            // User is already logged in, redirect to dashboard or profile page
           
        }

        $data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'"><img src="'.base_url('assets/uploads/google.png').'" alt="Login With Google" width="100%"></a>';
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
            if (!$userInfo) {
                // If user not found, respond with error message
                return $this->fail('Email not registered');
            }
            
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
               
                //session()->set('logged_user', $userInfo);
                // $session->set('user_id', $userInfo['id']);
                // $session->set('firstname', $userInfo['firstname']);
                session()->setFlashdata('success', 'Login success');
                return redirect()->to('dashboard')->withInput();
            }
            
     
        }
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
    
                // Check if the user is already registered
                $existingUser = $this->userModel->getUserByEmail($data['email']);
                if ($existingUser) {
                    // If user already exists, set userdata with existing user's ID
                    $userdata = [
                        'id' => $existingUser['id'],
                        'firstname' => $data['givenName'],
                        'lastname' => $data['familyName'],
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'updated_at' => $currentDateTime
                    ];
                    $this->userModel->updateUserData($userdata, $data['email']);
                    $this->setUserSession($userdata);
                } else {
                    // If user doesn't exist, insert new user data and retrieve the inserted ID
                    $userdata = [
                        'firstname' => $data['givenName'],
                        'lastname' => $data['familyName'],
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'created_at' => $currentDateTime
                    ];
                    $this->userModel->insertUserData($userdata);
                    $this->setUserSession($userdata);
                    
                }
                
                // Set the user ID in session
                //session()->set('google_user', $userdata);
                // session()->set('user_id', $userdata['id']);
                // session()->set('firstname', $userdata['firstname']);
               
                // Redirect to dashboard
                return redirect()->to(base_url("/dashboard"));
            } else {
                session()->setFlashdata("Error", "Something went wrong");
                return redirect()->to(base_url());
            }
        } else {
            session()->setFlashdata("Error", "Something went wrong");
            return redirect()->to(base_url());
        }
    }
    
    
     private function setUserSession($userInfo)
    {
        $data = [
            'id' => $userInfo['id'],
            'firstname' => $userInfo['firstname'],
            'lastname' => $userInfo['lastname'],
            'email' => $userInfo['email'],
            'isLoggedIn' => true,
        ];

        session()->set('logged_user',$data);
        return true;
     }
    public function logout()
    {
        // Remove session for logged_user and google_user
        session()->remove('logged_user');
       // session()->remove('google_user');
        
        // Redirect to the login page after logout
        return redirect()->to(base_url());
    }


    
 public function register()
    {
        if (! $this->request->is('post')) {
            return view('register');
        }
        
        // Define validation rules
        $rules = [
            'firstname' => [
                'rules' => 'required|min_length[3]|max_length[20]',
                'errors' => [
                    'required' => 'First name is required.',
                    'min_length' => 'First name must be at least 3 characters long.',
                    'max_length' => 'First name cannot exceed 20 characters.'
                ]
            ],
            'lastname' => [
                'rules' => 'required|min_length[3]|max_length[20]',
                'errors' => [
                    'required' => 'Last name is required.',
                    'min_length' => 'Last name must be at least 3 characters long.',
                    'max_length' => 'Last name cannot exceed 20 characters.'
                ]
            ],
            'email' => [
                'rules' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email is required.',
                    'min_length' => 'Email must be at least 6 characters long.',
                    'max_length' => 'Email cannot exceed 50 characters.',
                    'valid_email' => 'Please provide a valid email address.',
                    'is_unique' => 'Email is already registered.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]|max_length[255]',
                'errors' => [
                    'required' => 'Password is required.',
                    'min_length' => 'Password must be at least 8 characters long.',
                    'max_length' => 'Password cannot exceed 255 characters.'
                ]
            ],
            'password_confirm' => [
                'label' => 'confirm password',
                'rules' => 'matches[password]',
                'errors' => [
                    'matches' => 'Passwords do not match.'
                ]
            ]
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


    // }
    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->has('logged_user')) {
            // User is not logged in
            session()->setFlashdata("Error", "You have Logged Out, Please Login Again.");
            return redirect()->to(base_url());
        }
    
        // Get the logged-in user's data from the session
        $user = session()->get('logged_user');
    
        // Retrieve the contacts specific to the logged-in user
        $contactModel = new ContactModel();
        $contacts = $contactModel->where('user_id', $user['id'])->findAll();
        $data = [
            'contacts' => $contacts,
            //'user_name' => $user['firstname'] . ' ' . $user['lastname'] // Pass user's name to the view
        ];
    
        // Load the dashboard view with user's contacts and name
        echo view('templates/header');
        echo view('dashboard', $data);
        echo view('templates/footer');
    }
    
    
    
    public function profile() {
        // Check for session variables to ensure user is logged in

    
        $data = [];
        helper(['form']);
        $model = new UserModel();
    
        // Determine which session type is being used
       
        if ($this->request->getMethod() == 'post') {
            // Validation rules
            $rules = [
                'firstname' => 'required|min_length[3]|max_length[20]',
                'lastname' => 'required|min_length[3]|max_length[20]',
                'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            ];
    
            // Validate input data
            if (! $this->validate($rules)) {
                // If validation fails, store validation errors in $data['validation']
                $data['validation'] = $this->validator;
            } else {
                // If validation passes, prepare data for update
                $newData = [
                  
                    'firstname' => $this->request->getPost('firstname'),
                    'lastname' => $this->request->getPost('lastname'),
                    'email' => $this->request->getPost('email'),
                ];
    
                // If password is provided, add it to the new data array
    
                // Update user data
                $model->save($newData);
    
                // Set flash message to indicate successful update
                session()->setFlashdata('success', 'Successfully Updated');
    
                // Redirect user back to dashboard
                return redirect()->to('dashboard');
            }
        }
    
        // Retrieve user's data based on session ID
      
    
        // Load views
        echo view('templates/header', $data);
        echo view('profile');
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
    
public function forgotpassword()
{
    if (! $this->request->is('post')) {
        return view('forgotpassword');
    }

    // Define validation rules
    $rules = [
        'email' => 'required|valid_email',
    ];

    // Validate the data
    if (! $this->validate($rules)) {
        return view('forgotpassword', ['validation' => $this->validator]);
    }

    $email = $this->request->getVar('email');

    // Check if the email exists in the database
    $userModel = new UserModel();
    $user = $userModel->where('email', $email)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'Email address not found.');
    }

    // Generate a reset token
    $token = bin2hex(random_bytes(16));
    $userModel->update($user['id'], ['reset_token' => $token, 'reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour'))]);

    // Send the reset link via email
    $this->sendResetEmail($email, $token);

    // Set a success message in session data
    session()->setFlashdata('success', 'Password reset link has been sent to your email.');

    // Return the view with the success message
    return view('forgotpassword');
}

private function sendResetEmail($email, $token)
{
    $message = "
        <html>
        <head>
            <title>Password Reset</title>
        </head>
        <body>
            <h2>Password Reset</h2>
            <p>Please click the link below to reset your password.</p>
            <h4><a href='".base_url()."users/resetpassword/".$token."'>Reset My Password</a></h4>
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
    $emailService->setTo($email);
    $emailService->setSubject('Password Reset');
    $emailService->setMessage($message);

    if ($emailService->send()) {
        session()->setFlashdata('message', 'Password reset link sent to email');
    } else {
        $debugMessage = $emailService->printDebugger(['headers']);
        log_message('error', $debugMessage);
        session()->setFlashdata('message', 'Failed to send reset email: ' . $debugMessage);
    }
}


public function resetpassword()
{
    if (! $this->request->is('post')) {
        $token = $this->request->uri->getSegment(3);
        return view('resetpassword', ['token' => $token]);
    }

    // Define validation rules
    $rules = [
        'new_password' => 'required|min_length[8]|max_length[255]',
        'confirm_password' => 'required|matches[new_password]',
        'token' => 'required'
    ];

    // Validate the data
    if (! $this->validate($rules)) {
        return view('resetpassword', ['validation' => $this->validator]);
    }

    $token = $this->request->getVar('token');
    $newPassword = $this->request->getVar('new_password');

    // Check if the token is valid
    $userModel = new UserModel();
    $user = $userModel->where('reset_token', $token)->where('reset_expires >', date('Y-m-d H:i:s'))->first();

    if (!$user) {
        return redirect()->back()->with('error', 'Invalid or expired token.');
    }

    // Update the password
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $userModel->update($user['id'], ['password' => $newPasswordHash, 'reset_token' => null, 'reset_expires' => null]);

    // Set a success message in session data
    session()->setFlashdata('success', 'Password updated successfully');

    // Redirect to the login page
    return redirect()->to('/login');
}

    // public function forgotpassword()
    // {
    //     if (! $this->request->is('post')) {
         
    //        // return  view('templates/header');
    //         return view('forgotpassword');
    //         //return view('templates/footer'); // Assuming you have a view for changing password
    //     }
         
    //     // Define validation rules
    //     $rules = [
    //         'old_password' => 'required',
    //         'new_password' => 'required|min_length[8]|max_length[255]',
    //         'confirm_password' => 'required|matches[new_password]',
    //     ];
    
    //     // Get POST data
    //     $data = $this->request->getPost(array_keys($rules));
    
    //     // Validate the data
    //     if (! $this->validate($rules)) {
        
    //         return view('forgotpassword');
         
    //     }
    
    //     // Check if old password matches with the one in the database
    //     $userModel = new UserModel();
    //     $user = $userModel->where('email', session()->get('email'))->first();
    
    //     if (!password_verify($data['old_password'], $user['password'])) {
    //         // Old password does not match
    //         return redirect()->back()->withInput()->with('error', 'Old password is incorrect');
    //     }
    
    //     // Update the password
    //     $newPasswordHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
    //     $userModel->update($user['id'], ['password' => $newPasswordHash]);
    
    //     // Set a success message in session data
    //     session()->setFlashdata('success', 'Password updated successfully');
    
    //     // Return the view with the success message
    //     return view('forgotpassword');
        
    // }
    

	
    // public function logout() {
    //     $session = session();
    //     $session->destroy();
        
      
    //    return redirect()->to('/login');
    
    
	
}
