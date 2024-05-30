<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmailModel;
use CodeIgniter\API\ResponseTrait;

class EmailController extends BaseController
{
    use ResponseTrait;

    public function sendEmail($senderId, $recipientEmail, $subject, $body)
    {
        $emailService = \Config\Services::email();

        // Retrieve the sender's email using their ID
        $userModel = new UserModel();
        $sender = $userModel->find($senderId);

        if (!$sender) {
            return $this->respondCreated(['message' => 'Sender not found']);
        }

        $senderEmail = $sender['email'];

        $emailService->setFrom($senderEmail, 'Maria'); // Use sender's email
        $emailService->setTo($recipientEmail);
        $emailService->setSubject($subject);
        $emailService->setMessage($body);

        if ($emailService->send()) {
            // Email sent successfully, save it in the database
            $emailModel = new EmailModel();
            $emailModel->insert([
                'user_id' => $senderId,
                'recipient' => $recipientEmail, // Save recipient email directly
                'subject' => $subject,
                'body' => $body
            ]);

            return $this->respondCreated(['message' => 'Email sent successfully']);
            //return $this->sendEmail($senderId, $recipientEmail, $subject, $body);
        } else {
            $debugMessage = $emailService->printDebugger(['headers']);
            log_message('error', $debugMessage);
            return $this->respondCreated(['message' => 'Failed to send email: ' . $debugMessage]);
        }
    }
 // Endpoint to receive POST data and call sendEmail
 public function sendEmailFromPost()
 {
     // Get data from the request
     $senderId = $this->request->getVar('user_id');
     $recipientEmail = $this->request->getVar('recipient');
     $subject = $this->request->getVar('subject');
     $body = $this->request->getVar('body');

     // Validate the input
     if (!$senderId || !$recipientEmail || !$subject || !$body) {
         return $this->failValidationErrors('All fields are required: senderId, recipientEmail, subject, body');
     }

     // Call sendEmail method with extracted data
     return $this->sendEmail($senderId, $recipientEmail, $subject, $body);
 }

}
// Correct way to call the sendEmail method

