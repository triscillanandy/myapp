<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmailModel;
use App\Models\Emailattach;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Files\File;

class EmailController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function showEmailForm()
    {
        return view('email');
    }

    public function sendEmail($senderId, $recipientEmail, $subject, $body, $attachments = [])
    {
        $emailService = \Config\Services::email();

        $userModel = new UserModel();
        $sender = $userModel->find($senderId);

        if (!$sender) {
            return $this->respondCreated(['message' => 'Sender not found']);
        }

        $senderEmail = $sender['email'];

        $emailService->setFrom($senderEmail, 'Maria');
        $emailService->setTo($recipientEmail);
        $emailService->setSubject($subject);
        $emailService->setMessage($body);
        $emailService->setReplyTo("no-reply@test.com", "Mcash");

        foreach ($attachments as $attachment) {
            if ($attachment->isValid() && !$attachment->hasMoved()) {
                $emailService->attach($attachment->getTempName(), 'inline', $attachment->getName());
            }
        }

        if ($emailService->send()) {
            $emailModel = new EmailModel();
            $emailId = $emailModel->insert([
                'user_id' => $senderId,
                'recipient' => $recipientEmail,
                'subject' => $subject,
                'body' => $body
            ]);

            $emailattachModel = new Emailattach();
            foreach ($attachments as $attachment) {
                $emailattachModel->insert([
                    'attach_id' => $emailId,
                    'file_name' => $attachment->getName(),
                    'file_path' => $attachment->getTempName(),
                    'file_type' => $attachment->getMimeType(),
                    'file_size' => $attachment->getSize(),
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->respondCreated(['message' => 'Email sent successfully']);
        } else {
            $debugMessage = $emailService->printDebugger(['headers']);
            log_message('error', $debugMessage);
            return $this->respondCreated(['message' => 'Failed to send email: ' . $debugMessage]);
        }
    }

    public function sendEmailFromPost()
    {
        $senderId = $this->request->getVar('user_id');
        $recipientEmail = $this->request->getVar('recipient');
        $subject = $this->request->getVar('subject'); 
        $body = $this->request->getVar('body');
        $attachments = $this->request->getFiles()['attachments'] ?? [];

        // File upload and validation
        $validationRules = [
            'attachments' => [
                'label' => 'Attachments',
                'rules' => 'uploaded[attachments]|max_size[attachments,100000]|ext_in[attachments,png,jpg,jpeg,pdf,doc,docx]'
            ]
        ];

        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            return $this->failValidationErrors($errors);
        }

        return $this->sendEmail($senderId, $recipientEmail, $subject, $body, $attachments);
    }

    // No need for the separate uploadFiles() method
    
}
