<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email','user_id'];
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // public function getContactsByUserId($userId)
    // {
    //     return $this->where('user_id', $userId)->findAll();
    // }
}
