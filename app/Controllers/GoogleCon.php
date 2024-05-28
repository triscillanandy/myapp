<?php

namespace App\Controllers;

use App\Models\UserModel;

class GoogleCon extends BaseController
{
    public $userModel = NULL;
    private $googleClient = NULL;
    public $session;
    function __construct(){
       // require_once APPPATH. "vendor/autoload.php"; // Make sure this path is correct
        $this->userModel = new UserModel();
        $this->session = session();
        
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
        $data['googleButton'] = $this->googleClient->createAuthUrl().'" ><img src="'.base_url('assests/uploads/google.png').'" alt="Login With Google" width="100%"></a>';
        return view('login', $data);
    }

    public function profile()
    {
        if (!session()->get("LoggedUserData")) {
            session()->setFlashdata("Error", "You have Logged Out, Please Login Again.");
            return redirect()->to(base_url());
        }
        return view('profile');
    }

    public function loginWithGoogle()
    {
        $data = [];
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

    public function logout()
    {
        session()->remove('LoggedUserData');
        session()->remove('AccessToken');
        if (!session()->get('LoggedUserData') && !session()->get('AccessToken')) {
            session()->setFlashdata("Success", "Logout Successful");
            return redirect()->to(base_url());
        } else {
            session()->setFlashdata("Error", "Failed to Logout, Please Try Again");
            return redirect()->to(base_url("/profile"));
        }
    }
}
