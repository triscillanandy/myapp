<?php

namespace App\Models;

use CodeIgniter\Model;


class Contact extends Model
{
    protected $table = 'contacts_table';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'created_at', 'updated_at'];
    protected $useSoftDeletes = true;
}
