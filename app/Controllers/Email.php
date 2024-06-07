<?php

namespace App\Controllers;

use App\Models\EmailQueueModel;
use CodeIgniter\I18n\Time;
// use CodeIgniter\Controller;

class Email extends BaseController

{ 
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function send()
    {
        if (!$this->request->is('post')) {
            return view('email_form');
        }
    
        // Define validation rules
        $rules = [
            'email' => [
                'rules' => 'required|min_length[6]|max_length[50]|valid_email',
                'errors' => [
                    'required' => 'Email is required.',
                    'min_length' => 'Email must be at least 6 characters long.',
                    'max_length' => 'Email cannot exceed 50 characters.',
                    'valid_email' => 'Please provide a valid email address.',
                ]
            ],
            'subject' => [
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Subject is required.',
                    'min_length' => 'Subject must be at least 3 characters long.',
                    'max_length' => 'Subject cannot exceed 255 characters.'
                ]
            ],
            'message' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Message is required.'
                ]
            ],
            // 'attachs' => [
            //     'label' => 'Attachments',
            //     'rules' => 'uploaded[attachs]|max_size[attachs,100000]|ext_in[attachs,png,jpg,jpeg,pdf,doc,docx]'
            // ]
        ];
    
        // Validate the data
        if (!$this->validate($rules)) {
            return view('email_form', ['validation' => $this->validator]);
        }
    
        // Prepare new email data
        $newEmailData = [
            'email' => $this->request->getVar('email'),
            'subject' => $this->request->getVar('subject'),
            'message' => $this->request->getVar('message'),
            'attachs' => $this->handleAttachments($this->request->getFiles('attachs')),
            'sent' => 0,
            'attempts' => 0,
            'created_at' => new Time('now'),
        ];
    
        // Save the email to the queue
        $emailQueueModel = new EmailQueueModel();
        $emailQueueModel->save($newEmailData);
    
        // Set a success message in session data
        $session = session();
        $session->setFlashdata('success', 'Email has been queued for processing.');
    
        // Redirect to the email form or any desired page
        return redirect()->to('/send2');
    }
    
    private function handleAttachments($files)
    {
        if (!$files) {
            return '';
        }
    
        $attachs = [];
    
        foreach ($files as $file) {
            // Check if the current file is an array (multiple files) or an object (single file)
            if (is_array($file)) {
                foreach ($file as $singleFile) {
                    if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                        $newName = $singleFile->getRandomName();
                        $singleFile->move(WRITEPATH . 'uploads', $newName);
                        $attachs[] = WRITEPATH . 'uploads/' . $newName;
                    }
                }
            } else {
                // Handle single file upload
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads', $newName);
                    $attachs[] = WRITEPATH . 'uploads/' . $newName;
                }
            }
        }
    
        return implode(',', $attachs);
    }
    
}    