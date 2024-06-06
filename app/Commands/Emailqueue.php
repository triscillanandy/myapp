<?php

// namespace App\Commands;

// use CodeIgniter\CLI\BaseCommand;
// use CodeIgniter\CLI\CLI;
// use Config\Database;
// use App\Models\EmailQueueModel;
// use CodeIgniter\Email\Email;

// class Emailqueue extends BaseCommand
// {
//     /**
//      * The Command's Group
//      *
//      * @var string
//      */
//     protected $group = 'Emails';

//     /**
//      * The Command's Name
//      *
//      * @var string
//      */
//     protected $name = 'email:process';

//     /**
//      * The Command's Description
//      *
//      * @var string
//      */
//     protected $description = 'Process the email queue.';

//     /**
//      * The Command's Usage
//      *
//      * @var string
//      */
//     protected $usage = 'email:process [limit]';

//     /**
//      * The Command's Arguments
//      *
//      * @var array
//      */
//     protected $arguments = [
//         'limit' => 'The number of emails to process in a single run.'
//     ];

//     /**
//      * The Command's Options
//      *
//      * @var array
//      */
//     protected $options = [];

//     protected $db;
//     protected $emailQueueModel;
//     protected $email;

//     public function __construct()
//     {
      
//         $this->db = Database::connect();
//         $this->emailQueueModel = new EmailQueueModel();
//         $this->email = \Config\Services::email();
//     }

//     /**
//      * Actually execute a command.
//      *
//      * @param array $params
//      */
//     public function run(array $params)
//     {
//         $limit = $params[0] ?? 10;
        
//         $emails = $this->emailQueueModel->getBatch('0', $limit);
        
//         if (empty($emails)) {
//             CLI::write('No emails to process.', 'yellow');
//             return;
//         }

//         foreach ($emails as $email) {
//             $this->email->setFrom('email.SMTPUser');
//             $this->email->setTo($email['email']);
//             $this->email->setSubject($email['subject']);
//             $this->email->setMessage($email['message']);

//             if ($email['attachs']) {
//                 $attachments = explode(",", $email['attachs']);
//                 foreach ($attachments as $attachment) {
//                     $this->email->attach($attachment);
//                 }
//             }

//             if ($this->email->send()) {
//                 $this->emailQueueModel->update($email['id'], ['sent' => 1, 'sent_at' => new \CodeIgniter\I18n\Time('now')]);
//                 CLI::write('Email sent to: ' . $email['email'], 'green');
//             } else {
//                 $this->emailQueueModel->update($email['id'], ['attempts' => $email['attempts'] + 1]);
//                 CLI::write('Failed to send email to: ' . $email['email'], 'red');
//             }
//         }
//     }
// }


namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use App\Models\EmailQueueModel;
use App\Models\Contact;
use CodeIgniter\Email\Email;

class Emailqueue extends BaseCommand
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
    protected $name = 'email:process';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Process the email queue.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'email:process [limit]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'limit' => 'The number of emails to process in a single run.'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    protected $db;
    protected $emailQueueModel;
    protected $contactModel;
    protected $email;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->emailQueueModel = new EmailQueueModel();
        $this->contactModel = new Contact();
        $this->email = \Config\Services::email();
    }

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // Fetch contacts
        $contacts = $this->contactModel->findAll();
        
        // Define subject, message, and attachments
        $subject = "Your Subject Here";
        $message = "Your message content here";
        $attachments = [
            // List of attachment paths
        ];

        // Enqueue emails
        $this->emailQueueModel->enqueueContacts($contacts, $subject, $message, $attachments);

        CLI::write('Contacts have been enqueued.', 'green');

        // Process the email queue
        $limit = $params[0] ?? 10;
        
        $emails = $this->emailQueueModel->getBatch('0', $limit);
        
        if (empty($emails)) {
            CLI::write('No emails to process.', 'yellow');
            return;
        }

        foreach ($emails as $email) {
            $this->email->setFrom('email.SMTPUser');
            $this->email->setTo($email['email']);
            $this->email->setSubject($email['subject']);
            $this->email->setMessage($email['message']);

            if ($email['attachs']) {
                $attachments = explode(",", $email['attachs']);
                foreach ($attachments as $attachment) {
                    $this->email->attach($attachment);
                }
            }

            if ($this->email->send()) {
                $this->emailQueueModel->update($email['id'], ['sent' => 1, 'sent_at' => new \CodeIgniter\I18n\Time('now')]);
                CLI::write('Email sent to: ' . $email['email'], 'green');
            } else {
                $this->emailQueueModel->update($email['id'], ['attempts' => $email['attempts'] + 1]);
                CLI::write('Failed to send email to: ' . $email['email'], 'red');
            }
        }
    }
}
