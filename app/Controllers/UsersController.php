<?php namespace App\Controllers;

use App\Models\UserModel;


use CodeIgniter\API\ResponseTrait;
class UsersController extends BaseController
{
    use ResponseTrait;

    
    //protected $helpers = ['url', 'form'];

    public function __construct()
    {
        helper(['url', 'form']);
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
    } elseif ($userInfo['status'] != 1) {
        // If user status is not activated, respond with error message
        return $this->fail('Account not activated. Please check your email for activation link.');
    }

    // User is authenticated and activated, proceed with login
    // Here, you can set any necessary session data or generate tokens if needed
    // For example:
    // $this->setUserSession($userInfo);
    
    // Respond with success message
    return $this->respondCreated(['message' => 'Login success']);
}
    

    
	// private function setUserSession($userInfo){
        
	// 	$data = [
	// 		'id' => $userInfo['id'],
	// 		'firstname' => $userInfo['firstname'],
	// 		'lastname' => $userInfo['lastname'],
	// 		'email' => $userInfo['email'],
	// 		'isLoggedIn' => true,
	// 	];

	// 	session()->set($data);
	// 	return true;
	// }

    
 use ResponseTrait;
 public function register()
    {
        // if (! $this->request->is('post')) {
       
        
        // Define validation rules
        $rules = $this->validate([
            'firstname' => 'required|min_length[3]|max_length[20]',
            'lastname' => 'required|min_length[3]|max_length[20]',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'matches[password]',
        ]);

       

        // Validate the data
        if (!$rules) {
            $response = [
            
                'message' => $this->validator->getErrors()
        ];
            
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
       // return $this->respondCreated('success', 'Successful Registration. Please check your email to activate your account.');
        $response = [
            
            'message' => 'Successful Registration. Please check your email to activate your account.'
    ];
        return $this->respondCreated($response);
    
    
        // // Set a success message in session data
        // $session = session();
        // // $session->setFlashdata();

        // // Redirect to the homepage or login page
        // return redirect()->to('/login');
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

    // Get data from the request
    $data = [
        'firstname' => $this->request->getPost("firstname"),
        'lastname' => $this->request->getPost("lastname"),
        'email' => $this->request->getPost('email'),
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
    

	
    public function logout() {
        $session = session();
        $session->destroy();
        
      
       return redirect()->to('/login');}
    
    
	
}



