<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['firstname', 'lastname', 'email', 'password','profile_img', 'code', 'status', 'otp_code', 'otp_expires_at', 'two_factor_enabled'];

    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    protected $useSoftDeletes = true;

    
    public function isAlreadyRegister($email)
    {
        return $this->where('email', $email)->first();
    }

    public function updateUserData($data, $email)
    {
        return $this->where('email', $email)->set($data)->update();
    }

    public function insertUserData($data)
    {
        return $this->insert($data);
    }
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

  
    // namespace App\Models;
    
    // use CodeIgniter\Model;
    
    // class UserModel extends Model
    // {
    //     protected $table = 'user';
    //     protected $primaryKey = 'id';
    //     protected $allowedFields = ['oauth_id', 'email', 'profile_img', 'created_at', 'updated_at'];
    
    //     protected $createdField  = 'created_at';
    //     protected $updatedField  = 'updated_at';

    //     function isAlreadyRegister($authid){
    //         return $this->db->table('user')->getWhere(['oauth_id'=>$authid])->getRowArray()>0?true:false;
    //     }
    //     function updateUserData($userdata, $authid){
    //         $this->db->table("user")->where(['oauth_id'=>$authid])->update($userdata);
    //     }
    //     function insertUserData($userdata){
    //         $this->db->table("user")->insert($userdata);
    //     }
    
    
    // protected $beforeInsert = ['beforeInsert'];
    // protected $beforeUpdate = ['beforeUpdate'];

    // protected function beforeInsert(array $data){
    //   $data = $this->passwordHash($data);
    //   $data['data']['created_at'] = date('Y-m-d H:i:s');
  
    //   return $data;
    // }
  
    // protected function beforeUpdate(array $data){
    //   $data = $this->passwordHash($data);
    //   $data['data']['updated_at'] = date('Y-m-d H:i:s');
    //   return $data;
    // }
  
    // protected function passwordHash(array $data){
    //   if(isset($data['data']['password']))
    //     $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
  
    //   return $data;
    // }

 

   
}
