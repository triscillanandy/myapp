<?php

namespace App\Models;

use CodeIgniter\Model;

class Contactemail extends Model
{
    protected $table = 'contactemail';
    protected $primaryKey = 'id';
    protected $allowedFields = ['contactid', 'email_id'];

//     public function getEmailsByContactId($contactId)
//     {
//         return $this->where('contactid', $contactId)->findAll();
//     }

//     public function getContactsByEmailId($emailId)
//     {
//         return $this->where('email_id', $emailId)->findAll();
//     }
 }
