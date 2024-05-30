<?php

namespace App\Models;

use CodeIgniter\Model;

class Emailattach extends Model
{
    protected $table = 'emailattach';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email_id', 'file_name', 'file_path', 'uploaded_at'];
    protected $createdField  = 'uploaded_at';

    public function getAttachmentsByEmailId($emailId)
    {
        return $this->where('email_id', $emailId)->findAll();
    }
}
