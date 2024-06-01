<?php

namespace App\Controllers;

use CodeIgniter\Files\File;
use CodeIgniter\Email\Email;

class Upload extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        return view('upload_form', ['errors' => []]);
    }

    public function upload()
    {
        $validationRule = [
            'userfile' => [
                'label' => 'Image File',
                'rules' => [
                    'uploaded[userfile]',
                    'is_image[userfile]',
                    'mime_in[userfile,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                    'max_size[userfile,100000000]', // 10MB max
                ],
            ],
        ];

        if (! $this->validate($validationRule)) {
            return $this->response->setStatusCode(400)
                                  ->setJSON(['errors' => $this->validator->getErrors()]);
        }

        $img = $this->request->getFile('userfile');

        if ($img && ! $img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(WRITEPATH . 'uploads', $newName);

            // Send email with the uploaded file
            $this->sendEmailWithAttachment(WRITEPATH . 'uploads/' . $newName);

            return $this->response->setJSON(['success' => 'File uploaded successfully!']);
        }

        return $this->response->setStatusCode(400)
                              ->setJSON(['errors' => 'The file has already been moved.']);
    }

    protected function sendEmailWithAttachment(string $filePath)
    {
        $email = \Config\Services::email();

        $email->setTo('jonestriscillah@gmail.com');
        $email->setFrom('your@example.com', 'Your Name');
        $email->setSubject('Uploaded File');
        $email->setMessage('Please find the uploaded file attached.');

        $email->attach($filePath);

        $email->send();
    }
}
