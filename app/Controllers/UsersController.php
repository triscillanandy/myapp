<?php namespace App\Controllers;

use App\Models\UserModel;
use \Firebase\JWT\JWT;

use CodeIgniter\API\ResponseTrait;
class UsersController extends BaseController
{
    use ResponseTrait;

    
    //protected $helpers = ['url', 'form'];
   
     public $userModel = NULL;
     private $googleClient = NULL;
     public $session;
 
    public function __construct()
    {
        helper(['url', 'form']);
        $this->userModel = new UserModel();
        $this->session = session();
        
        $this->googleClient = new \Google\Client();
        $this->googleClient->setAuthConfig('G:/xampp/htdocs/myapp/clients.json');
        $this->googleClient->setRedirectUri("http://localhost/myapp/public/loginWithGoogle");
        $this->googleClient->addScope("email");
        $this->googleClient->addScope("profile");
        
    }
    
   
    public function cuth()
    {
        $users = new UserModel();
        return $this->respond(['users' => $users->findAll()], 200);
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
            // If validation fails, respond with error messages
            $response = [
                'message' => $this->validator->getErrors()
            ];
            return $this->respondCreated($response);
        }
    
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
            // If password is incorrect, respond with error message
            return $this->fail('Incorrect password');
        }
    
        if ($userInfo['status'] == 0) {
            // If user status is not activated, respond with error message
            return $this->fail('Account not activated. Please check your email for activation link.');
        }
    
        if ($userInfo['status'] == 1) {
            // Generate JWT
            $key = getenv('JWT_SECRET');
            $iat = time(); // current timestamp value
            $exp = $iat + 3600; // token expires in 1 hour
    
            $payload = [
                'iss' => 'your-issuer', // Issuer of the token
                'aud' => 'your-audience', // Audience of the token
                'sub' => $userInfo['id'], // Subject of the token (usually the user ID)
                'iat' => $iat, // Issued at: time when the token was generated
                'exp' => $exp, // Expiration time
                'email' => $userInfo['email']
            ];
    
            $token = JWT::encode($payload, $key, 'HS256');
            
            // Respond with the token
            return $this->respondCreated([
                'message' => 'Login successful',
                'token' => $token
            ]);
        }
    
        // Handle any other status cases if necessary
        return $this->fail('Unable to login due to an unknown status.');
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
                    $userdata = [
                        'firstname' => $data['givenName'],
                        'lastname' => $data['familyName'],
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'updated_at' => $currentDateTime
                    ];
                    $this->userModel->updateUserData($userdata, $data['email']);
                } else {
                    $userdata = [
                        'firstname' => $data['givenName'],
                        'lastname' => $data['familyName'],
                        'email' => $data['email'],
                        'profile_img' => $data['picture'],
                        'created_at' => $currentDateTime
                    ];
                    $this->userModel->insertUserData($userdata);
                }
                return $this->respondCreated([
                    'message' => 'Login successful',
             
                    'user' => $userdata
                ]);
            
               
            } else {
                return $this->respond([
                    'message' => 'Login successfulno',
             
                   
                ]);
            }
        } else {
            return $this->respond([
                'message' => 'Not logged in ',
         
               
            ]);
        }
    }

 
    public function register()
    {  
        
   
        $model = new UserModel();
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
        if ($this->validate($rules)) {
            $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = substr(str_shuffle($set), 0, 12);

            $newUserData = [
                'firstname' => $this->request->getVar('firstname'),
                'lastname' => $this->request->getVar('lastname'),
                'email' => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
                'code' => $code,
                'status' => 0, // Initially inactive
            ];

           
            $model->save($newUserData);
            $userId = $model->getInsertID();

            $this->sendVerificationEmail($this->request->getVar('email'), $userId, $code);

            return $this->respond(['message' => 'Successful Registration. Please check your email to activate your account.'], 200);
        } else {
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response, 409);
        }
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
      
        // $config['protocol'] = 'smtp';
        // $config['SMTPHost'] = 'smtp.gmail.com';
        // $config['SMTPUser'] = 'uprint332@gmail.com';
        // $config['SMTPPass'] = 'vhklocvwhgyhtydk';
        // $config['SMTPPort'] = 465;
        // $config['mailType'] = 'html'; // Set email format to HTML
        // $config['charset']  = 'utf-8'; // Set charset
        // $config['wordWrap'] = true; 

       
        // $emailService->initialize($config);
        // try {

        // } cat
        $emailService->setFrom('uprint332@gmail.com', 'maria');
        $emailService->setTo($recipientEmail);
        $emailService->setSubject('Signup Verification Email');
        $emailService->setMessage($message);
        
        
        if ($emailService->send()) {
          
            return $this->respondCreated(['message' => 'Activation code sent to email']);
        } else {
            $debugMessage = $emailService->printDebugger(['headers']);
            log_message('error', $debugMessage);
            return $this->respondCreated(['message' => 'Failed to send activation email'. $debugMessage]);
       
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
                return $this->respondCreated(['message' => 'User activated successfully']);
            } else {
                return $this->respond(['message' => 'Something went wrong in activating account'], 500);
            }
        } else {
            return $this->respond(['message' => 'Cannot activate account. Code did not match'], 400);
        }
    }
    
    
    public function profile($id)
    {
        $userModel = new UserModel();
        
        // Find user by ID
        $user = $userModel->find($id);
    
        // Check if user is found
        if ($user) {
            // Return a successful response with user data
            return $this->respond($user);
        } else {
            // If user is not found, return a not found response
            return $this->failNotFound('User not found');
        }
    }

//     public function update($id)
//     {
//     $user = new UserModel();
//     $data = [
//         'firstname' => $this->request->getPost("firstname"),
//         'lastname' => $this->request->getPost("lastname"),
//         'email' => $this->request->getPost('email'),

//     ];

//     $user->update($id, $data);
//     return redirect()->to(base_url("dashboard"))->with("status", " Updated Successfully");
// }
public function update($id)
{
    $userModel = new UserModel();

    // Find the user by ID
    $user = $userModel->find($id);

    // Check if user exists
    if (!$user) {
        return $this->failNotFound('User not found');
    }

    // Get data from the request
    $data = [
        'firstname' => $this->request->getVar("firstname"),
        'lastname' => $this->request->getVar("lastname") ,
        'email' => $this->request->getVar('email') 
    ];

    // Update user data
    $result = $userModel->update($id, $data);

    // Check if update is successful
    if ($result) {
        // Respond with a success message
        return $this->respondCreated(['status' => true, 'message' => 'Updated Successfully']);
    } else {
        // If update fails, respond with an error message
        return $this->fail('Failed to update user data');
    }
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


    public function forgotpassword($id)
{
    // Get the user by ID
    $userModel = new UserModel();
    $user = $userModel->find($id);

    // Check if user exists
    if (!$user) {
        return $this->failNotFound('User not found');
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
    if (!$this->validate($rules)) {
        // If validation fails, return validation errors
        return $this->failValidationErrors($this->validator->getErrors());
    }

    // Check if old password matches with the one in the database
    if (!password_verify($data['old_password'], $user['password'])) {
        // If old password does not match, return error
        return $this->fail('Old password is incorrect', 401);
    }

    // Update the password
    $newPasswordHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
    $result = $userModel->update($id, ['password' => $newPasswordHash]);

    if (!$result) {
        // If password update fails, return error response
        return $this->fail('Failed to update password');
    }

    // Password updated successfully, return success response
    return $this->respondUpdated(['message' => 'Password updated successfully']);
}

    

	
    public function logout() {
        $session = session();
        $session->destroy();
        
      
       return redirect()->to('/login');}
    
    
	
}



