<?php  
namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key column
    protected $allowedFields = ['email', 'token', 'created_at'];
    protected $returnType = 'array'; // Specify the return type
    
}
