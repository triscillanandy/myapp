<?php





namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class EmailQueueModel extends Model
{
    protected $table = 'email_queue';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email', 'subject', 'attachs', 'message', 'sent', 'sent_at', 'attempts', 'created_at'
    ];
    // protected $useSoftDeletes = true;

    public function enqueue($to, string $subject, array $attachs, array $data): bool
    {
        $defaults = [
            'email' => $to,
            'subject' => $subject,
    
            'attachs' => implode(",", $attachs),
            'message' => $data['message'],
            'created_at' => new Time('now'),
            'attempts' => 0,
            'sent' => 0
        ];

        return $this->insert($defaults);
    }

    public function getBatch($status = '0,1', $size = 100): array
    {
        return $this->asArray()
                    ->whereIn('sent', explode(',', $status))
                    ->where('attempts <=', 3)
                    ->limit($size)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // public function enqueueContacts(array $contacts, string $subject, string $message, array $attachments = []): void
    // {
    //     foreach ($contacts as $contact) {
    //         $this->enqueue($contact['email'], $subject, $attachments, ['message' => $message]);
    //     }
    // }
}
