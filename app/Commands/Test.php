<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class Test extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Emails';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'send:email';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'A command that\'s used to send emails in a queue';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        //
        // while (true) {

        //     print "sending emails ...";
        // }

       // $results = $this->db->table('users')->get()->getResult();
     
        // $results = $this->db->table('users');
       // $query   =  $results->get()->getResult(); 
          // $query = $results->get(10, 20)->getResult();
        
        // $results = $this->db->table('users')->select('firstname, email');
        
        // $query = $results->get()->getResult();
        // print_r($query);
        
    }
}
