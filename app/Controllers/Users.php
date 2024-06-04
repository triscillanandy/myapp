<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ContactModel;
use CodeIgniter\I18n\Time;
use \Firebase\JWT\JWT;

use CodeIgniter\API\ResponseTrait;
class Users extends BaseController
{
    use ResponseTrait;
    protected $helpers = ['url', 'form','date'];
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
        date_default_timezone_set('Africa/Nairobi'); // e.g., 'UTC', 'America/New_York'

    }

    // Check if user is logged in
   
    public function index()
    {
        if ($this->session->get("logged_user")) {
            session()->setFlashdata("Error", "You have Already Logged In");
            // User is already logged in, redirect to dashboard or profile page
           
        }
        $data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'"><img src="'.base_url('images/google.png').'" alt="Login With Google" height="20" width="20"></a>';

        //$data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'"><img src="'.base_url('images/google.png').'" alt="Login With Google" width="100%"></a>';
         //$data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'"><img src="'.base_url('assets/uploads/google.png').'" alt="Login With Google" width="100%"></a>';
         return view('login',$data);
        // $data['googleButton'] = '<a href="'.$this->googleClient->createAuthUrl().'"</a>';
       
      
    }


    // In App\Controllers\Users.php

private function generateOtp()
{
    return rand(100000, 999999); // Generate a 6-digit OTP
}

private function sendOtp($user, $method = 'email')
{
    $otp = $this->generateOtp();
    $now = new Time('now');
    $expiresAt = $now->addMinutes(10);

    $this->userModel->update($user['id'], [
        'otp_code' => $otp,
        'otp_expires_at' => $expiresAt->toDateTimeString(),
    ]);

    // Store OTP and expiration time in session
    $this->session->set('otp_code', $otp);
    $this->session->set('otp_expires_at', $expiresAt->toDateTimeString());

    if ($method == 'email') {
        $this->sendEmailOtp($user['email'], $otp);
    } elseif ($method == 'phone') {
        return $this->fail('Email not registered');
    }
}


private function sendEmailOtp($email, $otp)
{
    // Use your email service to send the OTP
    $emailService = \Config\Services::email();
    $emailService->setFrom('uprint332@gmail.com', 'Maria');
    $emailService->setTo($email);
    $emailService->setSubject('Your OTP Code');
    $emailService->setMessage("Your OTP code is: $otp");
    $emailService->send();
}

// private function sendSmsOtp($phone, $otp)
// {
//     // Use your SMS service to send the OTP
//     // Example using a fictional SMS service
//     $smsService = new SmsService();
//     $smsService->send($phone, "Your OTP code is: $otp");
// }

// In App\Controllers\Users.php
public function verifyOtp()
{
    $otp = $this->request->getVar('otp');
    $user = $this->session->get('logged_user');

    if (!$user || !$otp) {
        return redirect()->to('login')->with('error', 'Invalid OTP or session expired');
    }

    $current_time = new Time('now');
    $otp_expires_at = new Time($user['otp_expires_at']);

    if ($user['otp_code'] == $otp && $otp_expires_at->isAfter($current_time)) {
        // OTP is correct and not expired
        $this->userModel->update($user['email'], [
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        $this->setUserSession($user);
        return redirect()->to('dashboard');
    } else {
        return redirect()->to('verifyotp')->with('error', 'Invalid or expired OTP');
    }
}


public function verifyOtpPage()
{
    return view('verifyotp'); // Create a view file named verifyOtp.php
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
            return $this->fail('Email not registered');
        }

        $checkPassword = password_verify($password, $userInfo['password']);

        if (!$checkPassword) {
            session()->setFlashdata('fail', 'Incorrect password');
            return redirect()->to('login')->withInput();
        } elseif ($userInfo['status'] != 1) {
            session()->setFlashdata('fail', 'Account not activated. Please check your email for activation link.');
            return redirect()->to('login')->withInput();
        } else {
            // Generate and send OTP
            $this->sendOtp($userInfo, 'email'); // Assuming email OTP for this example
            $this->setUserSession($userInfo);
            // Store user info in session without OTP details
            

            return redirect()->to('verifyotp')->with('info', 'Please enter the OTP sent to your email to proceed.');
        }
    }
}



    // public function login()
    // {
      
    //     $validation = $this->validate([
    //         'email' => [
    //             'rules' => 'required|valid_email|is_not_unique[users.email]',
    //             'errors' => [
    //                 'required' => "Email Field Required",
    //                 'valid_email' => "Not a valid email",
    //                 'is_not_unique' => "Email not registered",
    //             ]
    //         ],
    //         'password' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => "Password Field Required"
    //             ]
    //         ],
    //     ]);
    
    //     if (!$validation) {
    //         return view('login', ['validation' => $this->validator]);
    //     } else {
    //         $email = $this->request->getVar('email');
    //         $password = $this->request->getVar('password');
    //         $userModel = new UserModel();
    
    //         $userInfo = $userModel->where('email', $email)->first();
    //         if (!$userInfo) {
    //             // If user not found, respond with error message
    //             return $this->fail('Email not registered');
    //         }
            
    //         $checkPassword = password_verify($password, $userInfo['password']);

    //         if (!$checkPassword) {
    //             session()->setFlashdata('fail', 'Incorrect password');
    //             return redirect()->to('login')->withInput();
    //         } elseif ($userInfo['status'] != 1) {
    //             // If user status is not activated, redirect to login with error message
    //             session()->setFlashdata('fail', 'Account not activated. Please check your email for activation link.');
    //             return redirect()->to('login')->withInput();
    //         } else {
    //             // User is activated, proceed with login
    //              $this->setUserSession($userInfo);
               
    //             //session()->set('logged_user', $userInfo);
    //             // $session->set('user_id', $userInfo['id']);
    //             // $session->set('firstname', $userInfo['firstname']);
    //             session()->setFlashdata('success', 'Login success');
    //             return redirect()->to('dashboard')->withInput();
    //         }
            
     
    //     }
    // }
    
    public function loginWithGoogle()
    {
        // Get Google Auth URL
        $googleAuthUrl = $this->googleClient->createAuthUrl();
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
                    $userInfo = [
                        'id' => $existingUser['id'],
                        'firstname' => $data['givenName'],
                        'lastname' => $data['familyName'],
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'updated_at' => $currentDateTime
                    ];
                    $this->userModel->updateUserData($userInfo, $data['email']);
                } else {
                    // If user doesn't exist, insert new user data and retrieve the inserted ID
                    $userInfo = [
                      
                        'firstname' => $data['givenName'],
                        'lastname' => $data['familyName'],
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'created_at' => $currentDateTime
                    ];
                    $userId = $this->userModel->insertUserData($userInfo);
                }
    
                // Generate and send OTP for both new and existing users
                $this->sendOtp($userInfo, 'email'); // Assuming email OTP for this example
                $this->setUserSession($userInfo);
    
                // Redirect to OTP verification page
                return redirect()->to('verifyotp')->with('info', 'Please enter the OTP sent to your email to proceed.');
            } else {
                session()->setFlashdata("Error", "Something went wrong");
                return redirect()->to(base_url());
            }
        } else {
            session()->setFlashdata("Error", "Something went wrong");
            return redirect()->to(base_url());
        }
    }
    
   
    // public function loginWithGoogle()


    // {
    //     // Get Google Auth URL
    //     $googleAuthUrl = $this->googleClient->createAuthUrl();
    //     if ($this->request->getVar('code')) {
    //         $token = $this->googleClient->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
    //         if (!isset($token['error'])) {
    //             $this->googleClient->setAccessToken($token['access_token']);
    //             session()->set("AccessToken", $token['access_token']);
    
    //             $googleService = new \Google\Service\Oauth2($this->googleClient);
    //             $data = $googleService->userinfo->get();
    //             $currentDateTime = date("Y-m-d H:i:s");
    
    //             // Check if the user is already registered
    //             $existingUser = $this->userModel->getUserByEmail($data['email']);
    //             if ($existingUser) {
    //                 // If user already exists, set userdata with existing user's ID
    //                 $userInfo = [
    //                     'id' => $existingUser['id'],
    //                     'firstname' => $data['givenName'],
    //                     'lastname' => $data['familyName'],
    //                     'email' => $data['email'],
    //                     'profile_img' => $data['picture'],
    //                     'updated_at' => $currentDateTime
    //                 ];
    //                 $this->userModel->updateUserData($userInfo, $data['email']);
    //                 $this->setUserSession($userInfo);
    //             } else {
    //                 // If user doesn't exist, insert new user data and retrieve the inserted ID
    //                 $userInfo = [
    //                     'firstname' => $data['givenName'],
    //                     'lastname' => $data['familyName'],
    //                     'email' => $data['email'],
    //                     'profile_img' => $data['picture'],
    //                     'created_at' => $currentDateTime
    //                 ];
    //                 $this->userModel->insertUserData($userInfo);
    //                 $this->setUserSession($userInfo);
                    
    //             }
       
    //             // Redirect to dashboard
    //             return redirect()->to(base_url("/dashboard"));
    //         } else {
    //             session()->setFlashdata("Error", "Something went wrong");
    //             return redirect()->to(base_url());
    //         }
    //     } else {
    //         session()->setFlashdata("Error", "Something went wrong");
    //         return redirect()->to(base_url());
    //     }
    // }
    
    
     private function setUserSession($userInfo)
    {
        $data = [
            'id' => $userInfo['id'],
            'firstname' => $userInfo['firstname'],
            'lastname' => $userInfo['lastname'],
            'email' => $userInfo['email'],
            'otp_code' => $this->session->get('otp_code'), // Retrieve the generated OTP
            'otp_expires_at' => $this->session->get('otp_expires_at'), // Ret
            
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
