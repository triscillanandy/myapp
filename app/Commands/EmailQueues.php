<?php


namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use App\Models\EmailQueueModel;
use App\Models\Contact;
use CodeIgniter\Email\Email;

class EmailQueues extends BaseCommand
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
    protected $name = 'email:processes';

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
    protected $usage = 'email:processes [limit]';

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

        $this->email = \Config\Services::email();
    }

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $lockFilePath = WRITEPATH . 'queue.txt';

        if (file_exists($lockFilePath)) {
            CLI::write("Queue is already running... bye!!", 'yellow');
            return;
        }

        // Create lock file
        $lockFile = fopen($lockFilePath, 'w');
        if ($lockFile === false) {
            CLI::write("Failed to create lock file.", 'red');
            return;
        }
        fclose($lockFile);

        try {
        
            


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

            
        } finally {
            //is  lock file is deleted
            if (file_exists($lockFilePath)) {
                unlink($lockFilePath);
            }
        }
    }
}