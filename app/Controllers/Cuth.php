<?php
  
namespace App\Controllers;
  


use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Cuth extends BaseController
{
    use ResponseTrait;
    public function inde()
    {
        $users = new UserModel();
        return $this->respond(['users' => $users->findAll()], 200);
    }

    public function index()
    {
        $userModel = new UserModel();
   
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
           
        $user = $userModel->where('email', $email)->first();
   
        if(is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
   
        $pwd_verify = password_verify($password, $user['password']);
   
        if(!$pwd_verify) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
  
        $key = env('JWT_SECRET', 'trtrt65643');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;
  
        $payload = array(
           
            "iss" => "issuer",
            "aud" => "audience",
            "sub" => "Subject of the JWT",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "email" => $user['email'],
           
          
        );
          
        $token = JWT::encode($payload, $key, 'HS256');
  
        $response = [
            'message' => 'Login Succesful',
            'token' => $token
        ];
          
        return $this->respond($response, 200);
    }
  
}
