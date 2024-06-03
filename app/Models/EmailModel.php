<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailModel extends Model
{
    protected $table = 'emails';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','recipient','subject', 'body','sent_at'];
    protected $createdField  = 'sent_at';

    public function getEmailsByUserId($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
}
