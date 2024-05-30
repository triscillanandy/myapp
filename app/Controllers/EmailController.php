<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmailModel;
use App\Models\Emailattach; // Include the Emailattach model
use CodeIgniter\API\ResponseTrait;

class EmailController extends BaseController
{
    use ResponseTrait;

    public function sendEmail($senderId, $recipientEmail, $subject, $body, $attachments = [])
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

        // Attach files if provided
        foreach ($attachments as $attachment) {
            $emailService->attach($attachment->getRealPath(), 'auto', $attachment->getMimeType());
        }

        if ($emailService->send()) {
            // Email sent successfully, save it in the database
            $emailModel = new EmailModel();
            $emailId = $emailModel->insert([
                'user_id' => $senderId,
                'recipient' => $recipientEmail,
                'subject' => $subject,
                'body' => $body
            ]);

            // Save attachment details using Emailattach model
            $emailattachModel = new Emailattach();
            foreach ($attachments as $attachment) {
                $emailattachModel->insert([
                    'attach_id' => $emailId,
                    'file_name' => $attachment->getName(),
                    'file_path' => $attachment->getRealPath(),
                    'file_type' => $attachment->getRealPath(),
                    'file_size' => $attachment->getSizeByUnit(),
                    'uploaded_at' => date('Y-m-d H:i:s') // Or you can use $attachment->getMTime() if needed
                ]);
            }

            return $this->respondCreated(['message' => 'Email sent successfully']);
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
        $attachments = $this->request->getFiles(); // Get uploaded files

        // Validate the input
        if (!$senderId || !$recipientEmail || !$subject || !$body) {
            return $this->failValidationErrors('All fields are required: senderId, recipientEmail, subject, body');
        }

        // Call sendEmail method with extracted data and attachments
        return $this->sendEmail($senderId, $recipientEmail, $subject, $body, $attachments);
    }
}
