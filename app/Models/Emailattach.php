<?php

namespace App\Models;

use CodeIgniter\Model;

class Emailattach extends Model
{
    protected $table = 'emailattach';
    protected $primaryKey = 'id';
    protected $allowedFields = ['attach_id', 'file_name', 'file_path','file_type','file_size', 'uploaded_at'];
    protected $createdField  = 'uploaded_at';

    // public function getAttachmentsByEmailId($emailId)
    // {
    //     return $this->where('email_id', $emailId)->findAll();
    // }
}
