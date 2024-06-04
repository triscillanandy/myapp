<?php


namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmailModel;
use App\Models\Emailattach;
use CodeIgniter\API\ResponseTrait;

class EmailController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function showEmailForm()
    {
        echo view('templates/header');
        return view('email');
        echo view('templates/footer');

     
       
    }

    public function sendEmail(int $senderId, string $recipientEmail, string $subject, string $body, array $attachments = [])
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
        // $emailService->setReplyTo("no-reply@test.com", "Mcash");

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
            if (!empty($attachments)) {
                $emailattachModel = new Emailattach();
                foreach ($attachments as $attachment) {
                    if ($attachment->isValid() && !$attachment->hasMoved()) {
                        $emailattachModel->insert([
                            'attach_id' => $emailId,
                            'file_name' => $attachment->getName(),
                            'file_path' => $attachment->getTempName(),
                            'file_type' => $attachment->getMimeType(),
                            'file_size' => $attachment->getSize(),
                            'uploaded_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            return redirect()->to('/email/form')->with('success', 'Email sent successfully.');
        } else {
            $debugMessage = $emailService->printDebugger(['headers']);
            log_message('error', $debugMessage);
            return $this->respondCreated(['message' => 'Failed to send email: ' . $debugMessage]);
        }
    }

    public function sendEmailFromPost()
    {
        if (!session()->has('logged_user')) {
            return $this->failUnauthorized('You must be logged in to send an email.');
        }

        $user = session()->get('logged_user');
        $senderId = $user['id'];

       
        $attachments = $this->request->getFiles()['attachments'] ?? [];

        // File upload and validation
        $validationRules = [
            'attachments' => [
                'label' => 'Attachments',
                // 'rules' => 'uploaded[attachments]|max_size[attachments,100000]|ext_in[attachments,png,jpg,jpeg,pdf,doc,docx]'
                'rules' => 'permit_empty|uploaded[attachments]|max_size[attachments,100000]|ext_in[attachments,png,jpg,jpeg,pdf,doc,docx]'
            ]
        ];

        $recipientEmail = $this->request->getVar('recipient');
        $subject = $this->request->getVar('subject');
        $body = $this->request->getVar('body');

        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            return $this->failValidationErrors($errors);
        }

        return $this->sendEmail($senderId, $recipientEmail, $subject, $body, $attachments);
    }


  
    
    public function listSentEmails()
    {
        // Check if user is logged in
        if (!session()->has('logged_user')) {
            // User is not logged in
            session()->setFlashdata("Error", "You have Logged Out, Please Login Again.");
            return redirect()->to(base_url());
        }
        $user = session()->get('logged_user');

        $emailModel = new EmailModel();
        $emails = $emailModel->where('user_id',  $user['id'])->findAll();
        $data = [
            'emails' =>  $emails,
            //'user_name' => $user['firstname'] . ' ' . $user['lastname'] // Pass user's name to the view
        ];
        echo view('templates/header');
        echo view('sent_email', $data);
        echo view('templates/footer');

    }

  
}
